import { useState, useEffect } from 'react';
import { 
  Search, 
  Trash2, 
  Check, 
  Sparkles, 
  ShoppingBag,
  Percent,
  Filter,
  ArrowUpDown,
  BookOpen,
  X
} from 'lucide-react';
import { fetchServices, createCustomPackage, fetchCustomPackages, getSuperlabCustomer } from '../services/api';
import { getSalePrice } from '../utils/pricing';

const MakeYourOwnPackagePage = () => {
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedTests, setSelectedTests] = useState([]);
  
  const initialAvailableTests = [
    { id: 1, name: 'HbA1c (Glycated Haemoglobin) Test', type: 'Recommended', category: 'Diabetes', price: 299, desc: 'Checks 3-month average blood glucose levels.' },
    { id: 2, name: 'Complete Blood Count (CBC) Test', type: 'Popular', category: 'General Health', price: 290, desc: 'Evaluates red cells, white cells, and platelets.' },
    { id: 3, name: 'Liver Function Test (LFT) Test', type: 'Recommended', category: 'Liver', price: 599, desc: 'Measures enzymes and proteins in the liver.' },
    { id: 4, name: 'Kidney Function Test (KFT) Test', type: 'Recommended', category: 'Kidney', price: 699, desc: 'Evaluates uric acid, urea, creatinine, and electrolytes.' },
    { id: 5, name: 'Lipid Profile Test', type: 'Popular', category: 'Heart', price: 450, desc: 'Measures cholesterol and triglyceride levels.' },
    { id: 6, name: 'Thyroid Profile (T3, T4, TSH) Test', type: 'Popular', category: 'Thyroid', price: 399, desc: 'Assesses thyroid gland activity and metabolism.' }
  ];

  const [availableTests, setAvailableTests] = useState(initialAvailableTests);

  useEffect(() => {
    fetchServices().then(({ data }) => {
      if (data && data.length > 0) {
        const apiTests = data.map(t => ({
          id: t.id,
          name: t.name,
          type: t.popular ? 'Popular' : '',
          category: t.category || 'General',
          price: getSalePrice(t) || 299,
          desc: t.description || 'Comprehensive diagnostic lab test.'
        }));
        setAvailableTests(apiTests);
      }
    });
  }, []);

  const loadSavedPackages = async () => {
    setSavedPackagesLoading(true);
    const result = await fetchCustomPackages();
    setSavedPackages(result.data || []);
    setSavedPackagesLoading(false);
  };

  useEffect(() => {
    loadSavedPackages();
    window.addEventListener('superlab_auth_update', loadSavedPackages);
    return () => window.removeEventListener('superlab_auth_update', loadSavedPackages);
  }, []);

  const addSavedPackageToCart = (pkg) => {
    if (!window.addToSuperlabCart) return;
    window.addToSuperlabCart({
      id: `custom-pkg-${pkg.id}`,
      customPackageId: pkg.id,
      name: pkg.name,
      price: pkg.total_price,
      originalPrice: pkg.subtotal_amount,
      type: 'package',
      category: 'Custom Package',
      isCustom: true,
      tests: (pkg.items || []).map((item) => item.name),
    });
    window.location.hash = '#/cart';
  };

  const handleCreatePackage = async () => {
    if (selectedTests.length === 0) {
      alert('Please add at least one test to your package!');
      return;
    }

    setIsCreating(true);
    const result = await createCustomPackage({
      items: selectedTests.map((test) => ({ id: test.id })),
    });
    setIsCreating(false);

    if (!result.success || !result.data) {
      alert(result.message || 'Could not save your custom package.');
      return;
    }

    const pkg = result.data;
    if (window.addToSuperlabCart) {
      window.addToSuperlabCart({
        id: `custom-pkg-${pkg.id}`,
        customPackageId: pkg.id,
        name: pkg.name,
        price: pkg.total_price,
        originalPrice: pkg.subtotal_amount,
        type: 'package',
        category: 'Custom Package',
        isCustom: true,
        tests: (pkg.items || selectedTests).map((t) => t.name),
      });
      window.location.hash = '#/cart';
    }

    if (isLoggedIn) {
      loadSavedPackages();
    }
  };

  // Handler to add/remove tests
  const toggleTest = (test) => {
    if (selectedTests.some(t => t.id === test.id)) {
      setSelectedTests(selectedTests.filter(t => t.id !== test.id));
    } else {
      setSelectedTests([...selectedTests, test]);
    }
  };

  // Calculations
  const originalTotal = selectedTests.reduce((sum, t) => sum + t.price, 0);
  
  // Discount Tiers
  // 10% OFF at ₹1500, 20% OFF at ₹2000, 25% OFF at ₹3000, 30% OFF at ₹4000
  let discountPercentage = 0;
  let nextTier = 1500;
  let nextDiscount = 10;
  
  if (originalTotal >= 4000) {
    discountPercentage = 30;
    nextTier = null;
  } else if (originalTotal >= 3000) {
    discountPercentage = 25;
    nextTier = 4000;
    nextDiscount = 30;
  } else if (originalTotal >= 2000) {
    discountPercentage = 20;
    nextTier = 3000;
    nextDiscount = 25;
  } else if (originalTotal >= 1500) {
    discountPercentage = 10;
    nextTier = 2000;
    nextDiscount = 20;
  }

  const [isFilterModalOpen, setIsFilterModalOpen] = useState(false);
  const [sortBy, setSortBy] = useState('popularity');
  const [selectedCategories, setSelectedCategories] = useState([]);
  const [isCreating, setIsCreating] = useState(false);
  const [savedPackages, setSavedPackages] = useState([]);
  const [savedPackagesLoading, setSavedPackagesLoading] = useState(false);

  const isLoggedIn = !!(getSuperlabCustomer()?.mobile || getSuperlabCustomer()?.name);

  // Extract unique categories
  const categoriesList = [...new Set(availableTests.map(t => t.category))].filter(Boolean);

  const handleCategoryCheckboxChange = (catName) => {
    if (selectedCategories.includes(catName)) {
      setSelectedCategories(selectedCategories.filter(c => c !== catName));
    } else {
      setSelectedCategories([...selectedCategories, catName]);
    }
  };

  const discountAmount = Math.round((originalTotal * discountPercentage) / 100);
  const finalTotal = originalTotal - discountAmount;
  const neededForNextTier = nextTier ? nextTier - originalTotal : 0;

  // Filter available tests based on search and selected categories
  let filteredTests = availableTests.filter(t => {
    const matchesSearch = t.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
                          t.category.toLowerCase().includes(searchQuery.toLowerCase());
    const matchesCategory = selectedCategories.length === 0 || selectedCategories.includes(t.category);
    return matchesSearch && matchesCategory;
  });

  // Apply sorting
  if (sortBy === 'low-to-high') {
    filteredTests = [...filteredTests].sort((a, b) => a.price - b.price);
  } else if (sortBy === 'high-to-low') {
    filteredTests = [...filteredTests].sort((a, b) => b.price - a.price);
  } else if (sortBy === 'popularity') {
    filteredTests = [...filteredTests].sort((a, b) => a.id - b.id);
  }

  return (
    <div className="test-detail-page-wrapper" style={{ backgroundColor: '#f8fafc', minHeight: '100vh', fontFamily: 'var(--sans)' }}>
      <style>{`
        .package-grid {
          display: grid;
          grid-template-columns: 1fr 400px;
          gap: 30px;
          align-items: start;
        }
        .search-wrapper {
          position: relative;
          margin-bottom: 24px;
        }
        .search-icon-inside {
          position: absolute;
          left: 16px;
          top: 50%;
          transform: translateY(-50%);
          color: var(--muted);
        }
        .package-search-input {
          width: 100%;
          padding: 14px 16px 14px 48px;
          border: 1px solid var(--line);
          border-radius: 12px;
          outline: none;
          font-size: 1rem;
          color: var(--ink);
          background-color: #ffffff;
          box-shadow: var(--shadow-sm);
          transition: all 0.2s;
        }
        .package-search-input:focus {
          border-color: var(--teal);
          box-shadow: 0 0 0 3px rgba(0, 163, 173, 0.15);
        }
        .test-selection-list {
          display: grid;
          grid-template-columns: 1fr;
          gap: 16px;
        }
        .package-test-card {
          background-color: #ffffff;
          border: 1px solid var(--line);
          border-radius: 16px;
          padding: 20px;
          display: flex;
          justify-content: space-between;
          align-items: center;
          box-shadow: var(--shadow-sm);
          transition: transform 0.2s, box-shadow 0.2s;
        }
        .package-test-card:hover {
          transform: translateY(-2px);
          box-shadow: var(--shadow-md);
        }
        .badge-recommended {
          background-color: var(--teal-soft);
          color: var(--teal-dark);
          font-size: 0.75rem;
          font-weight: 700;
          padding: 4px 8px;
          border-radius: 20px;
          display: inline-block;
          margin-bottom: 6px;
        }
        .badge-popular {
          background-color: #ffe0b2;
          color: #e65100;
          font-size: 0.75rem;
          font-weight: 700;
          padding: 4px 8px;
          border-radius: 20px;
          display: inline-block;
          margin-bottom: 6px;
        }
        .btn-toggle-test {
          border: none;
          border-radius: 8px;
          padding: 10px 24px;
          font-weight: 700;
          cursor: pointer;
          transition: all 0.2s;
          font-size: 0.95rem;
        }
        .btn-toggle-test.add {
          background-color: var(--orange);
          color: #ffffff;
        }
        .btn-toggle-test.add:hover {
          background-color: var(--orange-dark);
        }
        .btn-toggle-test.added {
          background-color: #fff3e0;
          color: var(--orange-dark);
          border: 1px solid var(--orange);
        }
        @media (max-width: 992px) {
          .package-grid {
            grid-template-columns: 1fr !important;
          }
        }
      `}</style>

      <div className="breadcrumbs-container">
        <div className="ribbon-breadcrumbs">
          <a href="#/" className="ribbon-breadcrumb-item">HOME</a>
          <div className="ribbon-breadcrumb-item active">CUSTOM PACKAGE</div>
        </div>
      </div>

      <div className="page-section-container">
        
        {/* Header Title */}
        <div style={{ marginBottom: '32px', textAlign: 'left' }}>
          <h1 style={{ fontSize: '2.4rem', fontWeight: 800, color: 'var(--blue)', margin: 0 }}>
            Make Your Own Package
          </h1>
          <p style={{ color: 'var(--muted)', fontSize: '1.1rem', marginTop: '8px' }}>
            Add tests to construct your own custom package and unlock massive discount benefits!
          </p>
        </div>

        {isLoggedIn && (
          <div style={{
            marginBottom: '28px',
            backgroundColor: '#ffffff',
            border: '1px solid var(--line)',
            borderRadius: '16px',
            padding: '20px 24px',
            textAlign: 'left',
          }}>
            <h2 style={{ fontSize: '1.1rem', fontWeight: '800', color: 'var(--blue)', margin: '0 0 12px 0' }}>
              Your saved packages
            </h2>
            {savedPackagesLoading ? (
              <p style={{ margin: 0, color: 'var(--muted)' }}>Loading saved packages...</p>
            ) : savedPackages.length === 0 ? (
              <p style={{ margin: 0, color: 'var(--muted)' }}>Packages you create will appear here for quick reorder.</p>
            ) : (
              <div style={{ display: 'flex', flexDirection: 'column', gap: '10px' }}>
                {savedPackages.map((pkg) => (
                  <div key={pkg.id} style={{
                    display: 'flex',
                    justifyContent: 'space-between',
                    alignItems: 'center',
                    gap: '16px',
                    padding: '12px 14px',
                    border: '1px solid var(--line)',
                    borderRadius: '12px',
                    backgroundColor: '#f8fafc',
                  }}>
                    <div>
                      <div style={{ fontWeight: '800', color: 'var(--blue)' }}>{pkg.name}</div>
                      <div style={{ fontSize: '0.85rem', color: 'var(--muted)' }}>
                        {pkg.test_count} tests · ₹{pkg.total_price}
                        {pkg.discount_percentage > 0 ? ` (${pkg.discount_percentage}% off)` : ''}
                      </div>
                    </div>
                    <button
                      type="button"
                      onClick={() => addSavedPackageToCart(pkg)}
                      style={{
                        backgroundColor: 'var(--teal)',
                        color: '#fff',
                        border: 'none',
                        borderRadius: '8px',
                        padding: '8px 14px',
                        fontWeight: '700',
                        cursor: 'pointer',
                        whiteSpace: 'nowrap',
                      }}
                    >
                      Add to cart
                    </button>
                  </div>
                ))}
              </div>
            )}
          </div>
        )}

        {/* Dynamic Grid Layout */}
        <div className="package-grid">
          
          {/* Left Column: Test Selection */}
          <div style={{ textAlign: 'left' }}>
            <div style={{ display: 'flex', gap: '12px', alignItems: 'center', marginBottom: '24px' }}>
              <div className="search-wrapper" style={{ flex: 1, marginBottom: 0 }}>
                <Search className="search-icon-inside" size={20} />
                <input 
                  type="text" 
                  placeholder="Search and add tests..." 
                  className="package-search-input"
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  style={{ padding: '10px 16px 10px 48px' }}
                />
              </div>
              <button 
                onClick={() => setIsFilterModalOpen(true)}
                style={{
                  display: 'flex',
                  alignItems: 'center',
                  gap: '8px',
                  height: '42px',
                  padding: '0 16px',
                  backgroundColor: '#ffffff',
                  border: '1px solid var(--line)',
                  borderRadius: '12px',
                  color: 'var(--blue)',
                  fontWeight: '700',
                  cursor: 'pointer',
                  boxShadow: 'var(--shadow-sm)',
                  transition: 'all 0.2s',
                  fontSize: '0.9rem'
                }}
              >
                <Filter size={16} />
                <span>Filters</span>
                {(selectedCategories.length > 0 || sortBy !== 'popularity') && (
                  <span style={{
                    width: '8px',
                    height: '8px',
                    backgroundColor: 'var(--teal)',
                    borderRadius: '50%'
                  }} />
                )}
              </button>
            </div>

            <div className="test-selection-list">
              {filteredTests.length > 0 ? (
                filteredTests.map((test) => {
                  const isAdded = selectedTests.some(t => t.id === test.id);
                  return (
                    <div key={test.id} className="package-test-card">
                      <div style={{ flex: 1, paddingRight: '20px' }}>
                        {test.type === 'Recommended' && <span className="badge-recommended">Recommended</span>}
                        {test.type === 'Popular' && <span className="badge-popular">Popular</span>}
                        <h3 style={{ fontSize: '1.2rem', fontWeight: '800', color: 'var(--blue)', margin: '0 0 6px 0' }}>
                          {test.name}
                        </h3>
                        <p style={{ color: 'var(--muted)', fontSize: '0.88rem', margin: '0 0 8px 0', lineHeight: '1.4' }}>
                          {test.desc}
                        </p>
                        <span style={{ fontSize: '0.85rem', fontWeight: '700', color: '#64748b', backgroundColor: '#f1f5f9', padding: '4px 10px', borderRadius: '12px' }}>
                          Category: {test.category}
                        </span>
                      </div>
                      
                      <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'flex-end', gap: '10px', flexShrink: 0 }}>
                        <span style={{ fontSize: '1.4rem', fontWeight: '800', color: 'var(--teal)' }}>₹ {test.price}</span>
                        <button 
                          onClick={() => toggleTest(test)} 
                          className={`btn-toggle-test ${isAdded ? 'added' : 'add'}`}
                        >
                          {isAdded ? (
                            <span style={{ display: 'flex', alignItems: 'center', gap: '4px' }}>
                              <Check size={16} /> ADDED
                            </span>
                          ) : 'ADD'}
                        </button>
                      </div>
                    </div>
                  );
                })
              ) : (
                <div style={{ backgroundColor: '#ffffff', borderRadius: '16px', padding: '40px', textAlign: 'center', border: '1px solid var(--line)', color: 'var(--muted)' }}>
                  No tests found matching your search.
                </div>
              )}
            </div>
          </div>

          {/* Right Column: Summary Panel */}
          <div style={{ position: 'sticky', top: '20px', display: 'flex', flexDirection: 'column', gap: '20px' }}>
            
            {/* Package Summary Card */}
            <div style={{
              backgroundColor: '#ffffff',
              border: '1px solid var(--line)',
              borderRadius: '20px',
              padding: '24px',
              boxShadow: 'var(--shadow-md)',
              textAlign: 'left'
            }}>
              <h3 style={{ fontSize: '1.25rem', fontWeight: '800', color: 'var(--blue)', margin: '0 0 16px 0', display: 'flex', alignItems: 'center', gap: '8px' }}>
                <ShoppingBag size={20} style={{ color: 'var(--teal)' }} />
                <span>Your Customized Package</span>
              </h3>

              {/* Progress & Discount Tracker */}
              <div style={{ backgroundColor: '#f0fdf4', border: '1px solid #bbf7d0', borderRadius: '12px', padding: '16px', marginBottom: '20px' }}>
                {discountPercentage > 0 ? (
                  <div style={{ color: '#166534', fontWeight: '700', display: 'flex', alignItems: 'center', gap: '6px', fontSize: '0.92rem' }}>
                    <Percent size={16} />
                    <span>{discountPercentage}% OFF Unlocked! You save ₹ {discountAmount}</span>
                  </div>
                ) : (
                  <div style={{ color: 'var(--muted)', fontWeight: '600', fontSize: '0.88rem' }}>
                    Add tests to build your customized package.
                  </div>
                )}
                
                {nextTier && (
                  <div style={{ fontSize: '0.85rem', color: '#15803d', marginTop: '6px', fontWeight: '500' }}>
                    Add <strong>₹ {neededForNextTier}</strong> more to unlock <strong>{nextDiscount}% OFF</strong>
                  </div>
                )}

                {/* Progress bar visual representation */}
                <div style={{ height: '6px', backgroundColor: '#e2edf6', borderRadius: '4px', marginTop: '10px', overflow: 'hidden' }}>
                  <div style={{
                    height: '100%',
                    backgroundColor: 'var(--teal)',
                    width: `${Math.min((originalTotal / 4000) * 100, 100)}%`,
                    transition: 'width 0.3s ease'
                  }} />
                </div>
              </div>

              {/* Added Tests List */}
              <div style={{ maxHeight: '180px', overflowY: 'auto', marginBottom: '20px', borderBottom: '1px solid var(--line)', paddingBottom: '16px' }}>
                {selectedTests.length > 0 ? (
                  <div style={{ display: 'flex', flexDirection: 'column', gap: '10px' }}>
                    {selectedTests.map((test) => (
                      <div key={test.id} style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', fontSize: '0.9rem' }}>
                        <span style={{ color: 'var(--blue)', fontWeight: '600', whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis', maxWidth: '240px' }}>
                          {test.name}
                        </span>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                          <span style={{ fontWeight: '700', color: 'var(--ink)' }}>₹ {test.price}</span>
                          <button 
                            onClick={() => toggleTest(test)}
                            style={{ background: 'none', border: 'none', color: '#ef4444', cursor: 'pointer', padding: 0 }}
                            title="Remove"
                          >
                            <Trash2 size={16} />
                          </button>
                        </div>
                      </div>
                    ))}
                  </div>
                ) : (
                  <div style={{ color: 'var(--muted)', fontSize: '0.9rem', textAlign: 'center', padding: '16px 0' }}>
                    No tests added yet.
                  </div>
                )}
              </div>

              {/* Pricing breakdown */}
              <div style={{ display: 'flex', flexDirection: 'column', gap: '8px', marginBottom: '20px', fontSize: '0.95rem' }}>
                <div style={{ display: 'flex', justifyContent: 'space-between', color: 'var(--muted)' }}>
                  <span>Subtotal ({selectedTests.length} tests)</span>
                  <span>₹ {originalTotal}</span>
                </div>
                {discountPercentage > 0 && (
                  <div style={{ display: 'flex', justifyContent: 'space-between', color: '#166534', fontWeight: '600' }}>
                    <span>Custom Discount ({discountPercentage}%)</span>
                    <span>- ₹ {discountAmount}</span>
                  </div>
                )}
                <div style={{ height: '1px', backgroundColor: 'var(--line)' }}></div>
                <div style={{ display: 'flex', justifyContent: 'space-between', fontSize: '1.2rem', fontWeight: '800', color: 'var(--blue)' }}>
                  <span>Total Price</span>
                  <span>₹ {finalTotal}</span>
                </div>
              </div>

              {/* Action Button */}
              <button 
                onClick={handleCreatePackage}
                disabled={selectedTests.length === 0 || isCreating}
                style={{
                  width: '100%',
                  backgroundColor: selectedTests.length > 0 && !isCreating ? 'var(--orange)' : '#cbd5e1',
                  color: '#ffffff',
                  border: 'none',
                  borderRadius: '10px',
                  padding: '14px',
                  fontWeight: '700',
                  fontSize: '1rem',
                  cursor: selectedTests.length > 0 && !isCreating ? 'pointer' : 'not-allowed',
                  transition: 'background-color 0.2s',
                  boxShadow: selectedTests.length > 0 ? '0 4px 6px rgba(255, 107, 0, 0.15)' : 'none'
                }}
                onMouseEnter={(e) => {
                  if (selectedTests.length > 0) e.target.style.backgroundColor = 'var(--orange-dark)';
                }}
                onMouseLeave={(e) => {
                  if (selectedTests.length > 0) e.target.style.backgroundColor = 'var(--orange)';
                }}
              >
                Create Package{isCreating ? '...' : ''}
              </button>
            </div>

            {/* Coupons & Offers Visual Guide Card */}
            <div style={{
              backgroundColor: '#ffffff',
              border: '1px solid var(--line)',
              borderRadius: '20px',
              padding: '20px',
              boxShadow: 'var(--shadow-sm)',
              textAlign: 'left'
            }}>
              <h4 style={{ fontSize: '0.95rem', fontWeight: '800', color: 'var(--blue)', margin: '0 0 12px 0', display: 'flex', alignItems: 'center', gap: '6px' }}>
                <Sparkles size={16} style={{ color: 'var(--orange)' }} />
                <span>Custom Package Benefits</span>
              </h4>
              <div style={{ display: 'flex', flexDirection: 'column', gap: '8px', fontSize: '0.85rem' }}>
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', padding: '6px 10px', borderRadius: '8px', backgroundColor: originalTotal >= 1500 ? '#f0fdf4' : 'transparent', border: originalTotal >= 1500 ? '1px solid #bbf7d0' : '1px solid transparent' }}>
                  <span style={{ fontWeight: '600', color: 'var(--blue)' }}>Tier 1 (10% OFF)</span>
                  <span style={{ color: 'var(--muted)' }}>Min. Value ₹ 1,500</span>
                </div>
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', padding: '6px 10px', borderRadius: '8px', backgroundColor: originalTotal >= 2000 ? '#f0fdf4' : 'transparent', border: originalTotal >= 2000 ? '1px solid #bbf7d0' : '1px solid transparent' }}>
                  <span style={{ fontWeight: '600', color: 'var(--blue)' }}>Tier 2 (20% OFF)</span>
                  <span style={{ color: 'var(--muted)' }}>Min. Value ₹ 2,000</span>
                </div>
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', padding: '6px 10px', borderRadius: '8px', backgroundColor: originalTotal >= 3000 ? '#f0fdf4' : 'transparent', border: originalTotal >= 3000 ? '1px solid #bbf7d0' : '1px solid transparent' }}>
                  <span style={{ fontWeight: '600', color: 'var(--blue)' }}>Tier 3 (25% OFF)</span>
                  <span style={{ color: 'var(--muted)' }}>Min. Value ₹ 3,000</span>
                </div>
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', padding: '6px 10px', borderRadius: '8px', backgroundColor: originalTotal >= 4000 ? '#f0fdf4' : 'transparent', border: originalTotal >= 4000 ? '1px solid #bbf7d0' : '1px solid transparent' }}>
                  <span style={{ fontWeight: '600', color: 'var(--blue)' }}>Tier 4 (30% OFF)</span>
                  <span style={{ color: 'var(--muted)' }}>Min. Value ₹ 4,000</span>
                </div>
              </div>
            </div>

          </div>

        </div>
      </div>

      {/* Filter Modal */}
      {isFilterModalOpen && (
        <div style={{
          position: 'fixed',
          top: 0,
          left: 0,
          right: 0,
          bottom: 0,
          backgroundColor: 'rgba(15, 23, 42, 0.6)',
          backdropFilter: 'blur(4px)',
          display: 'flex',
          justifyContent: 'center',
          alignItems: 'center',
          zIndex: 9999,
          padding: '20px'
        }}>
          <div style={{
            backgroundColor: '#ffffff',
            borderRadius: '20px',
            width: '100%',
            maxWidth: '480px',
            boxShadow: '0 25px 50px -12px rgba(0, 0, 0, 0.25)',
            display: 'flex',
            flexDirection: 'column',
            overflow: 'hidden'
          }}>
            {/* Modal Header */}
            <div style={{
              display: 'flex',
              justifyContent: 'space-between',
              alignItems: 'center',
              padding: '20px 24px',
              borderBottom: '1px solid var(--line)',
              textAlign: 'left'
            }}>
              <span style={{ fontWeight: '800', color: 'var(--blue)', fontSize: '1.25rem', display: 'flex', alignItems: 'center', gap: '8px' }}>
                <Filter size={20} style={{ color: 'var(--teal)' }} /> Filter & Sort
              </span>
              <button 
                onClick={() => setIsFilterModalOpen(false)}
                style={{ border: 'none', background: 'none', color: 'var(--muted)', cursor: 'pointer', padding: '4px' }}
              >
                <X size={24} />
              </button>
            </div>

            {/* Modal Content */}
            <div style={{ padding: '24px', display: 'flex', flexDirection: 'column', gap: '24px', overflowY: 'auto', maxHeight: '60vh', textAlign: 'left' }}>
              {/* Sort Section */}
              <div>
                <h4 style={{ fontSize: '0.95rem', fontWeight: '800', color: 'var(--blue)', display: 'flex', alignItems: 'center', gap: '6px', margin: '0 0 12px 0', textTransform: 'uppercase', letterSpacing: '0.5px' }}>
                  <ArrowUpDown size={16} /> Sort By
                </h4>
                <div style={{ display: 'flex', flexDirection: 'column', gap: '10px' }}>
                  {[
                    { label: 'Popularity', value: 'popularity' },
                    { label: 'Price: Low to High', value: 'low-to-high' },
                    { label: 'Price: High to Low', value: 'high-to-low' }
                  ].map((opt) => (
                    <label key={opt.value} style={{ display: 'flex', alignItems: 'center', gap: '10px', cursor: 'pointer', fontSize: '0.95rem', color: '#475569' }}>
                      <input 
                        type="radio" 
                        name="sort" 
                        value={opt.value} 
                        checked={sortBy === opt.value}
                        onChange={() => setSortBy(opt.value)}
                        style={{ accentColor: 'var(--teal)', width: '18px', height: '18px' }}
                      />
                      <span>{opt.label}</span>
                    </label>
                  ))}
                </div>
              </div>

              {/* Categories Section */}
              <div>
                <h4 style={{ fontSize: '0.95rem', fontWeight: '800', color: 'var(--blue)', display: 'flex', alignItems: 'center', gap: '6px', margin: '0 0 12px 0', textTransform: 'uppercase', letterSpacing: '0.5px' }}>
                  <BookOpen size={16} /> Categories
                </h4>
                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '12px' }}>
                  {categoriesList.map((cat) => (
                    <label key={cat} style={{ display: 'flex', alignItems: 'center', gap: '10px', cursor: 'pointer', fontSize: '0.95rem', color: '#475569' }}>
                      <input 
                        type="checkbox" 
                        checked={selectedCategories.includes(cat)}
                        onChange={() => handleCategoryCheckboxChange(cat)}
                        style={{ accentColor: 'var(--teal)', width: '18px', height: '18px', borderRadius: '4px' }}
                      />
                      <span>{cat}</span>
                    </label>
                  ))}
                </div>
              </div>
            </div>

            {/* Modal Footer */}
            <div style={{
              display: 'flex',
              justifyContent: 'space-between',
              alignItems: 'center',
              padding: '16px 24px',
              backgroundColor: '#f8fafc',
              borderTop: '1px solid var(--line)'
            }}>
              <button 
                onClick={() => {
                  setSelectedCategories([]);
                  setSortBy('popularity');
                }}
                style={{
                  border: 'none',
                  background: 'none',
                  color: '#ef4444',
                  fontWeight: '700',
                  fontSize: '0.95rem',
                  cursor: 'pointer',
                  padding: 0
                }}
              >
                Clear All
              </button>
              <button 
                onClick={() => setIsFilterModalOpen(false)}
                style={{
                  backgroundColor: 'var(--teal)',
                  color: '#ffffff',
                  border: 'none',
                  borderRadius: '10px',
                  padding: '10px 24px',
                  fontWeight: '700',
                  fontSize: '0.95rem',
                  cursor: 'pointer',
                  boxShadow: '0 4px 10px rgba(0, 163, 173, 0.2)'
                }}
              >
                Apply Filters
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default MakeYourOwnPackagePage;
