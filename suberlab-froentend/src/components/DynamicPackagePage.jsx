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
import { fetchPackageBySlug } from '../services/api';
import { formatDiscountPercent, getOriginalPrice } from '../utils/pricing';

const DynamicPackagePage = ({ packageSlug, setIsIsoModalOpen }) => {
  const [openFaqIndex, setOpenFaqIndex] = useState(null);
  const [isAdded, setIsAdded] = useState(false);
  const [openCategories, setOpenCategories] = useState({ 0: true });
  const [pkg, setPkg] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [notFound, setNotFound] = useState(false);

  // Parse human readable title from slug
  const formattedSlugTitle = (packageSlug || '')
    .replace(/-/g, ' ')
    .replace(/\b\w/g, c => c.toUpperCase());

  useEffect(() => {
    if (pkg?.name) {
      document.title = `${pkg.name} | SuperLab`;
      return;
    }
    if (notFound) {
      document.title = 'Package Not Found | SuperLab';
    }
  }, [pkg?.name, notFound]);

  useEffect(() => {
    let isMounted = true;
    setIsLoading(true);

    fetchPackageBySlug(packageSlug).then(({ data }) => {
      if (!isMounted) return;

      let matchedPkg = data;

      if (!matchedPkg) {
        setNotFound(true);
        setIsLoading(false);
        return;
      }

      const offerPrice = matchedPkg.offer_price ?? 1499;
      const originalPrice = getOriginalPrice(matchedPkg);
      const discountPct = originalPrice
        ? formatDiscountPercent(offerPrice, originalPrice)
        : (typeof matchedPkg.discount === 'number' ? matchedPkg.discount : null);

      setPkg({
        id: matchedPkg.id,
        name: matchedPkg.name || formattedSlugTitle,
        price: offerPrice,
        originalPrice,
        discount: discountPct != null ? `${discountPct}% OFF` : (matchedPkg.discount ? `${matchedPkg.discount}% OFF` : null),
        testsCount: matchedPkg.tests_included_count || matchedPkg.test_count || 60,
        description: matchedPkg.description || 'Verified Pathology Lab Package',
        test_components: matchedPkg.test_components || null,
        faqs: matchedPkg.faqs || null,
        fasting_condition: matchedPkg.fasting_condition || null,
      });
      setIsLoading(false);
    });

    return () => { isMounted = false; };
  }, [packageSlug]);

  useEffect(() => {
    const checkCart = () => {
      if (window.getSuperlabCart && pkg) {
        const cart = window.getSuperlabCart();
        setIsAdded(cart.some(item => 
          item.name && pkg.name && item.name.toLowerCase().trim() === pkg.name.toLowerCase().trim()
        ));
      }
    };
    checkCart();
    window.addEventListener('superlab_cart_update', checkCart);
    return () => window.removeEventListener('superlab_cart_update', checkCart);
  }, [pkg]);

  const handleToggleCart = () => {
    if (!pkg || !window.addToSuperlabCart || !window.removeFromSuperlabCart) return;
    const cartPayload = {
      id: pkg.id,
      name: pkg.name,
      price: pkg.price,
      type: 'package',
      testsCount: pkg.testsCount,
      ...(pkg.originalPrice ? { originalPrice: pkg.originalPrice } : {}),
    };
    if (isAdded) {
      window.removeFromSuperlabCart(pkg.name);
    } else {
      window.addToSuperlabCart(cartPayload);
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
      name: "Liver Function Test (LFT) - 11 parameters",
      tests: ["Bilirubin Total", "Bilirubin Direct", "Bilirubin Indirect", "SGOT (AST)", "SGPT (ALT)", "Alkaline Phosphatase (ALP)", "Total Protein", "Albumin", "Globulin", "A/G Ratio", "Gamma Glutamyl Transferase (GGT)"]
    },
    {
      name: "Kidney Function Test (KFT) - 10 parameters",
      tests: ["Urea", "Creatinine", "Uric Acid", "BUN (Blood Urea Nitrogen)", "BUN/Creatinine Ratio", "Calcium", "Phosphorus", "Sodium", "Potassium", "Chloride"]
    },
    {
      name: "Lipid Profile (Heart Health) - 8 parameters",
      tests: ["Total Cholesterol", "Triglycerides", "HDL Cholesterol", "LDL Cholesterol", "VLDL Cholesterol", "Non-HDL Cholesterol", "TC/HDL Ratio", "LDL/HDL Ratio"]
    },
    {
      name: "Thyroid Profile (Ultra-sensitive) - 3 parameters",
      tests: ["T3 (Triiodothyronine)", "T4 (Thyroxine)", "TSH (Thyroid Stimulating Hormone)"]
    },
    {
      name: "Complete Hemogram (Blood Health) - 24 parameters",
      tests: ["Haemoglobin", "RBC Count", "WBC Count", "Platelet Count", "PCV", "MCV", "MCH", "MCHC", "DLC (Neutrophils, Lymphocytes, Monocytes, Eosinophils, Basophils)", "Absolute counts"]
    }
  ];

  const defaultFaqs = [
    {
      q: `What is the ${pkg?.name || 'Full Body Package'}?`,
      a: "It is a comprehensive health checkup package comprising essential blood and urine tests that evaluate the functioning of vital organs like heart, kidney, liver, thyroid, and blood status."
    },
    {
      q: "Are there any fasting requirements?",
      a: "Yes, a minimum of 8 to 12 hours of overnight fasting is recommended. You may drink normal water during the fasting period."
    },
    {
      q: "When will I get my test reports?",
      a: "Electronic reports verified by qualified medical pathologists will be delivered via email and WhatsApp within 12 to 24 hours of sample collection."
    }
  ];

  const categories = (pkg?.test_components && pkg.test_components.length > 0)
    ? pkg.test_components.map(name => ({ name, tests: [] }))
    : defaultCategories;

  const faqs = (pkg?.faqs && pkg.faqs.length > 0)
    ? pkg.faqs.map(item => ({ q: item.question || item.q, a: item.answer || item.a }))
    : defaultFaqs;

  if (isLoading) {
    return (
      <div style={{ minHeight: '80vh', display: 'flex', alignItems: 'center', justifyContent: 'center', backgroundColor: '#f8fafc' }}>
        <div style={{ textAlign: 'center', color: 'var(--teal)' }}>
          <Clock className="animate-spin" size={40} style={{ marginBottom: '12px' }} />
          <h3 style={{ margin: 0, color: 'var(--blue)', fontWeight: 700 }}>Loading Package Details...</h3>
        </div>
      </div>
    );
  }

  if (notFound || !pkg) {
    return (
      <div style={{ minHeight: '80vh', display: 'flex', alignItems: 'center', justifyContent: 'center', backgroundColor: '#f8fafc', padding: '24px' }}>
        <div style={{ textAlign: 'center', maxWidth: '480px' }}>
          <h2 style={{ color: 'var(--blue)', marginBottom: '8px' }}>Package Not Found</h2>
          <p style={{ color: '#64748b', marginBottom: '20px' }}>
            We could not load <strong>{formattedSlugTitle || packageSlug}</strong> from the catalog.
          </p>
          <a href="#/lab-tests" style={{ color: '#00a3ad', fontWeight: 700, textDecoration: 'none' }}>Browse health packages</a>
        </div>
      </div>
    );
  }

  return (
    <div className="package-detail-page-wrapper" style={{ backgroundColor: '#f8fafc', minHeight: '100vh', fontFamily: 'var(--sans)' }}>
      
      {/* Breadcrumb Ribbon */}
      <div className="breadcrumbs-container">
        <div className="ribbon-breadcrumbs">
          <a href="#/" className="ribbon-breadcrumb-item">HOME</a>
          <a href="#/lab-tests" className="ribbon-breadcrumb-item">CATALOG</a>
          <div className="ribbon-breadcrumb-item active">{pkg.name.toUpperCase()}</div>
        </div>
      </div>

      <div className="page-section-container">
        
        {/* Hero Card */}
        <div className="test-hero-card">
          <div style={{ flex: 1, minWidth: 'min(300px, 100%)' }}>
            <div style={{ display: 'inline-flex', alignItems: 'center', gap: '6px', background: 'linear-gradient(135deg, #ea580c 0%, #c2410c 100%)', color: 'white', padding: '6px 14px', borderRadius: '10px', fontSize: '0.82rem', fontWeight: '800', marginBottom: '14px' }}>
              <span>Health Package ({pkg.discount})</span>
            </div>
            <h1 style={{ fontSize: '2.1rem', fontWeight: 800, color: 'var(--blue)', margin: 0, letterSpacing: '-0.3px', lineHeight: 1.2 }}>
              {pkg.name}
            </h1>
            <p style={{ color: 'var(--muted)', fontSize: '0.98rem', margin: '6px 0 0 0', fontWeight: '500' }}>
              {pkg.description}
            </p>
          </div>

          <div style={{ display: 'flex', alignItems: 'center', gap: '24px', flexWrap: 'wrap' }}>
            <div style={{ textAlign: 'right' }}>
              <span style={{ display: 'block', fontSize: '0.85rem', color: 'var(--muted)', fontWeight: '600' }}>Special Offer Price</span>
              <span style={{ fontSize: '2.2rem', fontWeight: '800', color: 'var(--teal)', lineHeight: 1 }}>₹ {pkg.price}</span>
              {pkg.originalPrice && pkg.originalPrice > pkg.price && (
                <span style={{ display: 'block', fontSize: '0.85rem', textDecoration: 'line-through', color: 'var(--muted)', marginTop: '2px' }}>₹ {pkg.originalPrice}</span>
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
          <span>FLAT 50% OFF ON FULL BODY PACKAGES - Book Today!*</span>
          <Info size={16} style={{ cursor: 'pointer' }} />
        </div>

        {/* Main Content Layout */}
        <div className="test-grid-layout">
          
          {/* Left Column */}
          <div>
            <div className="spec-card-grid">
              
              <div className="spec-item-card">
                <div className="spec-icon-wrapper">
                  <FileText size={18} />
                </div>
                <div>
                  <span style={{ display: 'block', fontSize: '0.78rem', color: 'var(--muted)', fontWeight: '700', textTransform: 'uppercase' }}>Parameters Count</span>
                  <span style={{ fontSize: '0.92rem', fontWeight: '700', color: 'var(--blue)' }}>{pkg.testsCount} Live Tests</span>
                </div>
              </div>

              <div className="spec-item-card">
                <div className="spec-icon-wrapper">
                  <Layers size={18} />
                </div>
                <div>
                  <span style={{ display: 'block', fontSize: '0.78rem', color: 'var(--muted)', fontWeight: '700', textTransform: 'uppercase' }}>Fasting Required</span>
                  <span style={{ fontSize: '0.92rem', fontWeight: '700', color: 'var(--blue)' }}>{pkg.fasting_condition || '10-12 Hours Fasting Recommended'}</span>
                </div>
              </div>

              <div className="spec-item-card">
                <div className="spec-icon-wrapper">
                  <Droplet size={18} />
                </div>
                <div>
                  <span style={{ display: 'block', fontSize: '0.78rem', color: 'var(--muted)', fontWeight: '700', textTransform: 'uppercase' }}>Sample Type</span>
                  <span style={{ fontSize: '0.92rem', fontWeight: '700', color: 'var(--blue)' }}>Blood & Urine</span>
                </div>
              </div>

              <div className="spec-item-card">
                <div className="spec-icon-wrapper">
                  <Clock size={18} />
                </div>
                <div>
                  <span style={{ display: 'block', fontSize: '0.78rem', color: 'var(--muted)', fontWeight: '700', textTransform: 'uppercase' }}>Report Delivery</span>
                  <span style={{ fontSize: '0.92rem', fontWeight: '700', color: 'var(--blue)' }}>Verified Report in 24 Hours</span>
                </div>
              </div>

            </div>

            {/* Parameters Accordion */}
            <div style={{ backgroundColor: '#ffffff', borderRadius: '16px', border: '1px solid var(--line)', padding: '24px', marginBottom: '32px' }}>
              <div style={{ display: 'flex', alignItems: 'center', gap: '10px', marginBottom: '20px' }}>
                <Layers size={22} style={{ color: 'var(--teal)' }} />
                <h3 style={{ fontSize: '1.25rem', fontWeight: 800, color: 'var(--blue)', margin: 0 }}>
                  Included Tests & Parameters ({pkg.testsCount})
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

            <HomeCollectionWorkflow />

          </div>

          {/* Right Column: Sticky Booking Card */}
          <div style={{ position: 'sticky', top: '90px' }}>
            <div style={{ backgroundColor: '#ffffff', borderRadius: '16px', border: '1px solid var(--line)', padding: '24px', boxShadow: '0 10px 25px -5px rgba(0,0,0,0.05)' }}>
              
              <div style={{ marginBottom: '20px' }}>
                <span style={{ fontSize: '0.85rem', color: 'var(--muted)', fontWeight: '600' }}>Selected Package</span>
                <h4 style={{ fontSize: '1.2rem', fontWeight: '800', color: 'var(--blue)', margin: '4px 0 0 0' }}>
                  {pkg.name}
                </h4>
              </div>

              <div style={{ display: 'flex', alignItems: 'baseline', gap: '8px', marginBottom: '20px' }}>
                <span style={{ fontSize: '2rem', fontWeight: '800', color: 'var(--teal)' }}>₹ {pkg.price}</span>
                {pkg.originalPrice && pkg.originalPrice > pkg.price && (
                  <span style={{ fontSize: '1rem', textDecoration: 'line-through', color: 'var(--muted)' }}>₹ {pkg.originalPrice}</span>
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
                onClick={handleToggleCart}
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

        {/* FAQs */}
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

        <ReviewsSection
          packageId={pkg?.id}
          packageSlug={packageSlug}
          itemName={pkg?.name || formattedSlugTitle}
        />

      </div>
    </div>
  );
};

export default DynamicPackagePage;
