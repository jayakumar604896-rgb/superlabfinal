import {
  Calendar,
  Check,
  ChevronDown,
  ChevronUp,
  Clock,
  Droplet,
  FileText,
  HelpCircle,
  Home,
  Info,
  Layers,
  ShoppingCart,
  Users
} from 'lucide-react';
import { useEffect, useState } from 'react';
import HomeCollectionWorkflow from './HomeCollectionWorkflow';
import ReviewsSection from './ReviewsSection';
import { fetchServiceBySlug } from '../services/api';
import { getOriginalPrice, getSalePrice } from '../utils/pricing';
import { getMockTestDatabase, isMockFallbackEnabled } from '../services/mockCatalog';

const DynamicTestPage = ({ testSlug, setIsIsoModalOpen }) => {
  const [openFaqIndex, setOpenFaqIndex] = useState(null);
  const [openCategories, setOpenCategories] = useState({ 0: true });
  const [isAdded, setIsAdded] = useState(false);
  const [test, setTest] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [notFound, setNotFound] = useState(false);

  // Parse human readable title from slug
  const formattedSlugTitle = (testSlug || '')
    .replace(/-/g, ' ')
    .replace(/\b\w/g, c => c.toUpperCase());

  useEffect(() => {
    if (test?.name) {
      document.title = `${test.name} | SuperLab`;
      return;
    }
    if (notFound) {
      document.title = 'Test Not Found | SuperLab';
    }
  }, [test?.name, notFound]);

  useEffect(() => {
    let isMounted = true;
    setIsLoading(true);

    fetchServiceBySlug(testSlug).then(async ({ data }) => {
      if (!isMounted) return;

      let matchedTest = data;

      if (!matchedTest && isMockFallbackEnabled()) {
        const mockDatabase = await getMockTestDatabase();
        matchedTest = mockDatabase.find((item) => item.slug === testSlug);
      }

      if (!matchedTest) {
        setNotFound(true);
        setIsLoading(false);
        return;
      }

      setTest({
        id: matchedTest.id,
        name: matchedTest.name || matchedTest.title || formattedSlugTitle,
        category: typeof matchedTest.category === 'object' ? matchedTest.category?.name : (matchedTest.category || 'Pathology'),
        price: getSalePrice(matchedTest) || 299,
        originalPrice: getOriginalPrice(matchedTest),
        sampleType: matchedTest.sampleType || matchedTest.sample_type || 'Blood',
        fastingRequired: matchedTest.fasting_condition || matchedTest.fastingRequired || matchedTest.fasting_required || 'No Fasting Required',
        reportTime: matchedTest.reportTime || matchedTest.report_time || 'Same Day (6-8 Hours)',
        alsoKnownAs: matchedTest.alsoKnownAs || matchedTest.alias || `${matchedTest.name || formattedSlugTitle} Panel`,
        gender: matchedTest.gender || 'Both',
        ageGroup: matchedTest.ageGroup || 'All Age Groups',
        homeCollectionAvailable: matchedTest.home_collection_available !== undefined ? Boolean(matchedTest.home_collection_available) : true,
        test_components: matchedTest.test_components || null,
        faqs: matchedTest.faqs || null,
      });
      setIsLoading(false);
    });

    return () => { isMounted = false; };
  }, [testSlug]);

  // Sync cart state
  useEffect(() => {
    const checkCart = () => {
      if (window.getSuperlabCart && test) {
        const cart = window.getSuperlabCart();
        setIsAdded(cart.some(item => 
          item.name && test.name && item.name.toLowerCase().trim() === test.name.toLowerCase().trim()
        ));
      }
    };
    checkCart();
    window.addEventListener('superlab_cart_update', checkCart);
    return () => window.removeEventListener('superlab_cart_update', checkCart);
  }, [test]);

  const handleCartToggle = () => {
    if (!test || !window.addToSuperlabCart || !window.removeFromSuperlabCart) return;
    if (isAdded) {
      window.removeFromSuperlabCart(test.name);
    } else {
      window.addToSuperlabCart({
        id: test.id,
        name: test.name,
        category: test.category,
        price: test.price,
        ...(test.originalPrice ? { originalPrice: test.originalPrice } : {}),
        type: 'test'
      });
    }
  };

  const toggleCategory = (idx) => {
    setOpenCategories(prev => ({
      ...prev,
      [idx]: !prev[idx]
    }));
  };

  const defaultCategories = [
    {
      name: `Primary Parameter - 1 test`,
      tests: [
        `${test?.name || 'Diagnostic Test'} - Quantitative analysis measuring biological concentration and clinical indicators.`
      ]
    },
    {
      name: "Why it is measured (Clinical Significance)",
      tests: [
        `Detecting and evaluating clinical indicators related to ${test?.category || 'Pathology'}.`,
        "Screening for underlying biochemical or nutritional imbalances.",
        "Providing vital quantitative metrics for physician diagnostic evaluation.",
        "Monitoring response to prescribed treatment or lifestyle modifications."
      ]
    }
  ];

  const defaultFaqs = [
    {
      q: `What is a ${test?.name || 'Lab Test'}?`,
      a: `A ${test?.name || 'diagnostic test'} measures key health indicators in blood or fluid samples to provide an accurate picture of your overall wellness.`
    },
    {
      q: `What is the purpose of ${test?.name}?`,
      a: `It helps physicians diagnose medical conditions, monitor disease progression, and customize effective health treatment plans.`
    },
    {
      q: `Do I need to fast before taking ${test?.name}?`,
      a: `${test?.fastingRequired || 'No Fasting Required'}. Follow standard fluid intake unless specifically instructed otherwise by your doctor.`
    },
    {
      q: `How long does it take to get reports for ${test?.name}?`,
      a: `Reports are generally verified by senior certified pathologists and delivered electronically within ${test?.reportTime || 'Same Day (6-8 Hours)'}.`
    }
  ];

  const categories = (test?.test_components && test.test_components.length > 0)
    ? test.test_components.map(name => ({ name, tests: [] }))
    : defaultCategories;

  const faqs = (test?.faqs && test.faqs.length > 0)
    ? test.faqs.map(item => ({ q: item.question || item.q, a: item.answer || item.a }))
    : defaultFaqs;

  if (isLoading) {
    return (
      <div style={{ minHeight: '80vh', display: 'flex', alignItems: 'center', justifyContent: 'center', backgroundColor: '#f8fafc' }}>
        <div style={{ textAlign: 'center', color: 'var(--teal)' }}>
          <Clock className="animate-spin" size={40} style={{ marginBottom: '12px' }} />
          <h3 style={{ margin: 0, color: 'var(--blue)', fontWeight: 700 }}>Loading Test Details...</h3>
        </div>
      </div>
    );
  }

  if (notFound || !test) {
    return (
      <div style={{ minHeight: '80vh', display: 'flex', alignItems: 'center', justifyContent: 'center', backgroundColor: '#f8fafc', padding: '24px' }}>
        <div style={{ textAlign: 'center', maxWidth: '480px' }}>
          <h2 style={{ color: 'var(--blue)', marginBottom: '8px' }}>Test Not Found</h2>
          <p style={{ color: '#64748b', marginBottom: '20px' }}>
            We could not load <strong>{formattedSlugTitle || testSlug}</strong> from the catalog. It may be unavailable or the server is offline.
          </p>
          <a href="#/lab-tests" style={{ color: '#00a3ad', fontWeight: 700, textDecoration: 'none' }}>Browse all lab tests</a>
        </div>
      </div>
    );
  }

  return (
    <div className="test-detail-page-wrapper" style={{ backgroundColor: '#f8fafc', minHeight: '100vh', fontFamily: 'var(--sans)' }}>
      
      {/* Breadcrumb Ribbon */}
      <div className="breadcrumbs-container">
        <div className="ribbon-breadcrumbs">
          <a href="#/" className="ribbon-breadcrumb-item">HOME</a>
          <a href="#/lab-tests" className="ribbon-breadcrumb-item">CATALOG</a>
          <div className="ribbon-breadcrumb-item active">{test.name.toUpperCase()}</div>
        </div>
      </div>

      <div className="page-section-container">
        
        {/* Test Hero Card */}
        <div className="test-hero-card">
          <div style={{ flex: 1, minWidth: 'min(300px, 100%)' }}>
            <div style={{ display: 'inline-flex', alignItems: 'center', gap: '6px', background: 'linear-gradient(135deg, #00b2b2 0%, #008080 100%)', color: 'white', padding: '6px 14px', borderRadius: '10px', fontSize: '0.82rem', fontWeight: '800', marginBottom: '14px' }}>
              <span>{test.category.replace(/\s*[tT]est\s*$/, '')} Test</span>
            </div>
            <h1 style={{ fontSize: '2.1rem', fontWeight: 800, color: 'var(--blue)', margin: 0, letterSpacing: '-0.3px', lineHeight: 1.2 }}>
              {test.name}
            </h1>
            <p style={{ color: 'var(--muted)', fontSize: '0.98rem', margin: '6px 0 0 0', fontWeight: '500' }}>
              Also known as {test.alsoKnownAs} • NABL Certified Lab Verification
            </p>
          </div>

          <div style={{ display: 'flex', alignItems: 'center', gap: '24px', flexWrap: 'wrap' }}>
            <div style={{ textAlign: 'right' }}>
              <span style={{ display: 'block', fontSize: '0.85rem', color: 'var(--muted)', fontWeight: '600' }}>Starting from</span>
              <span style={{ fontSize: '2.2rem', fontWeight: '800', color: 'var(--teal)', lineHeight: 1 }}>₹ {test.price}</span>
              {test.originalPrice && test.originalPrice > test.price && (
                <span style={{ display: 'block', fontSize: '0.85rem', textDecoration: 'line-through', color: 'var(--muted)', marginTop: '2px' }}>₹ {test.originalPrice}</span>
              )}
            </div>
            
            <div style={{ display: 'flex', flexDirection: 'column', gap: '8px', textAlign: 'left' }}>
              <div style={{ display: 'flex', alignItems: 'center', gap: '8px', padding: '6px 12px', backgroundColor: '#e8f5e9', border: '1px solid #c8e6c9', borderRadius: '20px', fontSize: '0.8rem', fontWeight: '700', color: '#2e7d32' }}>
                <span style={{ display: 'inline-block', width: '6px', height: '6px', backgroundColor: '#2e7d32', borderRadius: '50%' }}></span>
                <span>NABL Accredited Partner</span>
              </div>
              <div style={{ display: 'flex', alignItems: 'center', gap: '8px', padding: '6px 12px', backgroundColor: '#e0f7fa', border: '1px solid #b2ebf2', borderRadius: '20px', fontSize: '0.8rem', fontWeight: '700', color: '#006064' }}>
                <span style={{ display: 'inline-block', width: '6px', height: '6px', backgroundColor: '#006064', borderRadius: '50%' }}></span>
                <span>ISO-15189 Certified</span>
              </div>
            </div>
          </div>
        </div>

        {/* Promo Banner */}
        <div style={{
          backgroundColor: 'var(--teal-soft)',
          border: '1px solid #b2ebf2',
          borderRadius: '8px',
          padding: '12px 16px',
          display: 'flex',
          alignItems: 'center',
          gap: '8px',
          color: 'var(--teal-dark)',
          fontWeight: '700',
          fontSize: '0.95rem',
          marginBottom: '30px'
        }}>
          <span>FLAT 25% OFF ON ALL TESTS - Book Today!*</span>
          <Info size={16} style={{ cursor: 'pointer' }} />
        </div>

        {/* Main Content Grid */}
        <div className="test-grid-layout">
          
          {/* Left Column: Specifications */}
          <div>
            <div className="spec-card-grid">
              
              <div className="spec-item-card">
                <div className="spec-icon-wrapper">
                  <FileText size={18} />
                </div>
                <div>
                  <span style={{ display: 'block', fontSize: '0.78rem', color: 'var(--muted)', fontWeight: '700', textTransform: 'uppercase' }}>Test Name</span>
                  <span style={{ fontSize: '0.92rem', fontWeight: '700', color: 'var(--blue)' }}>{test.name}</span>
                </div>
              </div>

              <div className="spec-item-card">
                <div className="spec-icon-wrapper">
                  <Layers size={18} />
                </div>
                <div>
                  <span style={{ display: 'block', fontSize: '0.78rem', color: 'var(--muted)', fontWeight: '700', textTransform: 'uppercase' }}>Category</span>
                  <span style={{ fontSize: '0.92rem', fontWeight: '700', color: 'var(--blue)' }}>{test.category.replace(/\s*[tT]est\s*$/, '')}</span>
                </div>
              </div>

              <div className="spec-item-card">
                <div className="spec-icon-wrapper">
                  <Droplet size={18} />
                </div>
                <div>
                  <span style={{ display: 'block', fontSize: '0.78rem', color: 'var(--muted)', fontWeight: '700', textTransform: 'uppercase' }}>Sample Type</span>
                  <span style={{ fontSize: '0.92rem', fontWeight: '700', color: 'var(--blue)' }}>{test.sampleType}</span>
                </div>
              </div>

              <div className="spec-item-card">
                <div className="spec-icon-wrapper">
                  <Users size={18} />
                </div>
                <div>
                  <span style={{ display: 'block', fontSize: '0.78rem', color: 'var(--muted)', fontWeight: '700', textTransform: 'uppercase' }}>Gender</span>
                  <span style={{ fontSize: '0.92rem', fontWeight: '700', color: 'var(--blue)' }}>{test.gender}</span>
                </div>
              </div>

              <div className="spec-item-card">
                <div className="spec-icon-wrapper">
                  <Calendar size={18} />
                </div>
                <div>
                  <span style={{ display: 'block', fontSize: '0.78rem', color: 'var(--muted)', fontWeight: '700', textTransform: 'uppercase' }}>Age Group</span>
                  <span style={{ fontSize: '0.92rem', fontWeight: '700', color: 'var(--blue)' }}>{test.ageGroup}</span>
                </div>
              </div>

              <div className="spec-item-card">
                <div className="spec-icon-wrapper">
                  <Info size={18} />
                </div>
                <div>
                  <span style={{ display: 'block', fontSize: '0.78rem', color: 'var(--muted)', fontWeight: '700', textTransform: 'uppercase' }}>Fasting Required</span>
                  <span style={{ fontSize: '0.92rem', fontWeight: '700', color: 'var(--blue)' }}>{test.fastingRequired}</span>
                </div>
              </div>

              <div className="spec-item-card">
                <div className="spec-icon-wrapper">
                  <Clock size={18} />
                </div>
                <div>
                  <span style={{ display: 'block', fontSize: '0.78rem', color: 'var(--muted)', fontWeight: '700', textTransform: 'uppercase' }}>Report Time</span>
                  <span style={{ fontSize: '0.92rem', fontWeight: '700', color: 'var(--blue)' }}>{test.reportTime}</span>
                </div>
              </div>

            </div>

            {/* Test Parameters Breakdown */}
            <div style={{ backgroundColor: '#ffffff', borderRadius: '16px', border: '1px solid var(--line)', padding: '24px', marginBottom: '32px' }}>
              <div style={{ display: 'flex', alignItems: 'center', gap: '10px', marginBottom: '20px' }}>
                <Layers size={22} style={{ color: 'var(--teal)' }} />
                <h3 style={{ fontSize: '1.25rem', fontWeight: 800, color: 'var(--blue)', margin: 0 }}>
                  Test Parameters Included ({categories.length})
                </h3>
              </div>

              <div style={{ display: 'flex', flexDirection: 'column', gap: '12px' }}>
                {categories.map((cat, idx) => (
                  <div key={idx} style={{ border: '1px solid var(--line)', borderRadius: '12px', overflow: 'hidden' }}>
                    <button
                      onClick={() => toggleCategory(idx)}
                      style={{
                        width: '100%',
                        padding: '14px 18px',
                        backgroundColor: openCategories[idx] ? '#f8fafc' : '#ffffff',
                        border: 'none',
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'space-between',
                        cursor: 'pointer',
                        fontWeight: '700',
                        color: 'var(--blue)',
                        fontSize: '0.95rem'
                      }}
                    >
                      <span>{cat.name}</span>
                      {cat.tests && cat.tests.length > 0 && (openCategories[idx] ? <ChevronUp size={18} /> : <ChevronDown size={18} />)}
                    </button>
                    {openCategories[idx] && cat.tests && cat.tests.length > 0 && (
                      <div style={{ padding: '16px 18px', backgroundColor: '#ffffff', borderTop: '1px solid var(--line)' }}>
                        <ul style={{ margin: 0, paddingLeft: '20px', color: '#475569', fontSize: '0.9rem', lineHeight: '1.6' }}>
                          {cat.tests.map((tItem, tIdx) => (
                            <li key={tIdx} style={{ marginBottom: '6px' }}>{tItem}</li>
                          ))}
                        </ul>
                      </div>
                    )}
                  </div>
                ))}
              </div>
            </div>

            {/* Collection Availability Badge */}
            {test.homeCollectionAvailable && (
              <div className="shining-speciality-badge" style={{ marginBottom: '32px' }}>
                <Home size={20} className="badge-icon-pulse" />
                <span style={{ position: 'relative', zIndex: 2 }}>Home Sample Collection Available</span>
              </div>
            )}

            {/* Workflow Component */}
            <HomeCollectionWorkflow />

          </div>

          {/* Right Column: Sticky Booking Card */}
          <div style={{ position: 'sticky', top: '90px' }}>
            <div style={{ backgroundColor: '#ffffff', borderRadius: '16px', border: '1px solid var(--line)', padding: '24px', boxShadow: '0 10px 25px -5px rgba(0,0,0,0.05)' }}>
              
              <div style={{ marginBottom: '20px' }}>
                <span style={{ fontSize: '0.85rem', color: 'var(--muted)', fontWeight: '600' }}>Selected Test</span>
                <h4 style={{ fontSize: '1.2rem', fontWeight: '800', color: 'var(--blue)', margin: '4px 0 0 0' }}>
                  {test.name}
                </h4>
              </div>

              <div style={{ display: 'flex', alignItems: 'baseline', gap: '8px', marginBottom: '20px' }}>
                <span style={{ fontSize: '2rem', fontWeight: '800', color: 'var(--teal)' }}>₹ {test.price}</span>
                {test.originalPrice && test.originalPrice > test.price && (
                  <span style={{ fontSize: '1rem', textDecoration: 'line-through', color: 'var(--muted)' }}>₹ {test.originalPrice}</span>
                )}
              </div>

              <div style={{ display: 'flex', flexDirection: 'column', gap: '10px', marginBottom: '24px', fontSize: '0.88rem', color: '#475569' }}>
                <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                  <Check size={16} style={{ color: 'var(--teal)' }} />
                  <span>Free Sample Collection at Home</span>
                </div>
                <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                  <Check size={16} style={{ color: 'var(--teal)' }} />
                  <span>100% Digital Verified Reports</span>
                </div>
                <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                  <Check size={16} style={{ color: 'var(--teal)' }} />
                  <span>Certified Pathologist Consultation</span>
                </div>
              </div>

              <button
                onClick={handleCartToggle}
                style={{
                  width: '100%',
                  padding: '14px',
                  backgroundColor: isAdded ? '#fff3e0' : 'var(--orange)',
                  color: isAdded ? 'var(--orange-dark)' : '#ffffff',
                  border: isAdded ? '1.5px solid var(--orange)' : 'none',
                  borderRadius: '10px',
                  fontWeight: '800',
                  fontSize: '1rem',
                  cursor: 'pointer',
                  display: 'flex',
                  alignItems: 'center',
                  justifyContent: 'center',
                  gap: '8px',
                  transition: 'all 0.2s'
                }}
              >
                <ShoppingCart size={18} />
                <span>{isAdded ? 'Added to Cart ✓' : 'Add to Cart'}</span>
              </button>

            </div>
          </div>

        </div>

        {/* FAQs Accordion Section */}
        <div style={{ backgroundColor: '#ffffff', borderRadius: '16px', border: '1px solid var(--line)', padding: '32px', marginTop: '40px' }}>
          <div style={{ display: 'flex', alignItems: 'center', gap: '10px', marginBottom: '24px' }}>
            <HelpCircle size={24} style={{ color: 'var(--teal)' }} />
            <h3 style={{ fontSize: '1.4rem', fontWeight: 800, color: 'var(--blue)', margin: 0 }}>
              Frequently Asked Questions (FAQs)
            </h3>
          </div>

          <div style={{ display: 'flex', flexDirection: 'column', gap: '12px' }}>
            {faqs.map((faq, idx) => (
              <div key={idx} style={{ border: '1px solid var(--line)', borderRadius: '12px', overflow: 'hidden' }}>
                <button
                  onClick={() => setOpenFaqIndex(openFaqIndex === idx ? null : idx)}
                  style={{
                    width: '100%',
                    padding: '16px 20px',
                    backgroundColor: openFaqIndex === idx ? '#f8fafc' : '#ffffff',
                    border: 'none',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'space-between',
                    cursor: 'pointer',
                    fontWeight: '700',
                    color: 'var(--blue)',
                    fontSize: '0.98rem',
                    textAlign: 'left'
                  }}
                >
                  <span>{faq.q}</span>
                  {openFaqIndex === idx ? <ChevronUp size={18} /> : <ChevronDown size={18} />}
                </button>
                {openFaqIndex === idx && (
                  <div style={{ padding: '16px 20px', backgroundColor: '#ffffff', borderTop: '1px solid var(--line)', color: '#475569', fontSize: '0.92rem', lineHeight: '1.6' }}>
                    {faq.a}
                  </div>
                )}
              </div>
            ))}
          </div>
        </div>

        {/* Reviews Section */}
        <ReviewsSection
          serviceId={test?.id}
          serviceSlug={testSlug}
          itemName={test?.name || formattedSlugTitle}
        />

      </div>
    </div>
  );
};

export default DynamicTestPage;
