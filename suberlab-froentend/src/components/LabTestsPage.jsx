import { useState, useEffect } from 'react';
import { 
  Search, 
  ChevronDown, 
  ChevronUp, 
  Filter, 
  ArrowUpDown, 
  BookOpen, 
  Check,
  Clock
} from 'lucide-react';
import { fetchServices, fetchCategories, fetchPackages } from '../services/api';
import { getOriginalPrice, getSalePrice } from '../utils/pricing';
import { getMockTestDatabase, isMockFallbackEnabled } from '../services/mockCatalog';

const LabTestsPage = () => {

  // States
  const [viewType, setViewType] = useState(() => {
    const viewTypePref = sessionStorage.getItem('superlab_view_type');
    if (viewTypePref) {
      sessionStorage.removeItem('superlab_view_type');
      return viewTypePref;
    }
    return 'all';
  }); // 'all', 'test', 'package'
  const [searchQuery, setSearchQuery] = useState(() => {
    const query = sessionStorage.getItem('superlab_search_query');
    if (query) {
      sessionStorage.removeItem('superlab_search_query');
      return query;
    }
    return '';
  });
  const [sortBy, setSortBy] = useState('popularity'); // 'popularity', 'low-to-high', 'high-to-low'
  const [selectedCategories, setSelectedCategories] = useState(() => {
    const categoryPref = sessionStorage.getItem('superlab_selected_category');
    if (categoryPref) {
      sessionStorage.removeItem('superlab_selected_category');
      return [categoryPref];
    }
    return [];
  });
  const [addedItems, setAddedItems] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [testsData, setTestsData] = useState([]);
  const [dynamicCategories, setDynamicCategories] = useState([]);
  
  // Accordion toggle states
  const [isSortOpen, setIsSortOpen] = useState(true);
  const [isCategoryOpen, setIsCategoryOpen] = useState(true);

  // Fetch tests, packages and categories from Laravel API backend
  useEffect(() => {
    let isMounted = true;
    setIsLoading(true);
    Promise.all([fetchServices(), fetchPackages(), fetchCategories()]).then(async ([servRes, pkgRes, catRes]) => {
      if (!isMounted) return;
      const services = (servRes.data || []).map(s => ({
        id: `test-${s.id}`,
        name: s.name || s.title,
        slug: s.slug,
        category: typeof s.category === 'object' ? s.category?.name : (s.category || 'Diagnostic Test'),
        price: getSalePrice(s) || 150,
        originalPrice: getOriginalPrice(s),
        type: 'test',
        popular: !!s.popular,
      }));

      const packages = (pkgRes.data || []).map(p => ({
        id: `package-${p.id}`,
        name: p.name,
        slug: p.slug,
        category: 'Full Body Health',
        price: p.offer_price ?? 1499,
        originalPrice: getOriginalPrice(p),
        testsCount: p.tests_included_count || p.tests_count || 60,
        type: 'package',
        popular: !!p.popular,
      }));

      let rawCombined = [...services, ...packages];
      if (rawCombined.length === 0 && isMockFallbackEnabled()) {
        rawCombined = await getMockTestDatabase();
      }
      
      // Deduplicate by item name
      const uniqueCombined = [];
      const seenNames = new Set();
      for (const item of rawCombined) {
        const key = item.name ? item.name.toLowerCase().trim() : '';
        if (key && !seenNames.has(key)) {
          seenNames.add(key);
          uniqueCombined.push(item);
        }
      }

      setTestsData(uniqueCombined);

      if (catRes.data && catRes.data.length > 0) {
        setDynamicCategories(catRes.data.map(c => c.name));
      } else {
        const extracted = [...new Set(uniqueCombined.map(i => i.category))];
        setDynamicCategories(extracted);
      }
      setIsLoading(false);
    });
    return () => { isMounted = false; };
  }, []);

  // Sync addedItems state with localStorage cart on load & updates
  useEffect(() => {
    const syncAddedItems = () => {
      if (window.getSuperlabCart) {
        const cart = window.getSuperlabCart();
        const dbIdsInCart = testsData
          .filter(dbItem => cart.some(cartItem => cartItem.name === dbItem.name))
          .map(dbItem => dbItem.id);
        setAddedItems(dbIdsInCart);
      }
    };
    window.addEventListener('superlab_cart_update', syncAddedItems);
    syncAddedItems();
    return () => window.removeEventListener('superlab_cart_update', syncAddedItems);
  }, [testsData]);

  // Sync category selection trigger from homepage View All buttons
  useEffect(() => {
    const syncCategoryTrigger = () => {
      const categoryPref = sessionStorage.getItem('superlab_selected_category');
      if (categoryPref) {
        setSelectedCategories([categoryPref]);
        sessionStorage.removeItem('superlab_selected_category');
      }
    };
    window.addEventListener('superlab_category_trigger', syncCategoryTrigger);
    syncCategoryTrigger();
    return () => window.removeEventListener('superlab_category_trigger', syncCategoryTrigger);
  }, []);

  // Sync search query from global header search redirect
  useEffect(() => {
    const syncSearchQuery = () => {
      const query = sessionStorage.getItem('superlab_search_query');
      if (query) {
        setSearchQuery(query);
        sessionStorage.removeItem('superlab_search_query');
      }
    };
    window.addEventListener('superlab_search_trigger', syncSearchQuery);
    return () => window.removeEventListener('superlab_search_trigger', syncSearchQuery);
  }, []);

  // Extract unique categories for filter list
  const categoriesList = dynamicCategories.length > 0 ? dynamicCategories : [...new Set(testsData.map(item => item.category))];

  // Filter and sort logic
  const getFilteredItems = () => {
    let items = [...testsData];

    // Filter by type (Test vs Package)
    if (viewType === 'test') {
      items = items.filter(item => item.type === 'test');
    } else if (viewType === 'package') {
      items = items.filter(item => item.type === 'package');
    }

    // Filter by search query
    if (searchQuery.trim() !== '') {
      items = items.filter(item => 
        item.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
        item.category.toLowerCase().includes(searchQuery.toLowerCase())
      );
    }

    // Filter by selected categories checkboxes
    if (selectedCategories.length > 0) {
      items = items.filter(item => selectedCategories.includes(item.category));
    }

    // Sorting
    if (sortBy === 'low-to-high') {
      items.sort((a, b) => a.price - b.price);
    } else if (sortBy === 'high-to-low') {
      items.sort((a, b) => b.price - a.price);
    } else if (sortBy === 'popularity') {
      items.sort((a, b) => (b.popular ? 1 : 0) - (a.popular ? 1 : 0));
    }

    return items;
  };

  const filteredItems = getFilteredItems();

  const handleCategoryCheckboxChange = (catName) => {
    if (selectedCategories.includes(catName)) {
      setSelectedCategories(selectedCategories.filter(c => c !== catName));
    } else {
      setSelectedCategories([...selectedCategories, catName]);
    }
  };

  const handleItemToggle = (itemId) => {
    const item = testsData.find(t => t.id === itemId);
    if (!item) return;

    const cart = window.getSuperlabCart ? window.getSuperlabCart() : [];
    const exists = cart.some(i => i.id === item.id || i.name === item.name);

    if (exists) {
      if (window.removeFromSuperlabCart) {
        window.removeFromSuperlabCart(item);
      }
    } else {
      if (window.addToSuperlabCart) {
        window.addToSuperlabCart({
          id: item.id || item.name.toLowerCase().replace(/\s+/g, '-'),
          name: item.name,
          category: item.category,
          price: item.price,
          ...(item.originalPrice ? { originalPrice: item.originalPrice } : {}),
          type: item.type || 'test'
        });
      }
    }
  };

  if (isLoading) {
    return (
      <div style={{ minHeight: '80vh', display: 'flex', alignItems: 'center', justifyContent: 'center', backgroundColor: '#f8fafc' }}>
        <div style={{ textAlign: 'center', color: 'var(--teal)' }}>
          <Clock className="animate-spin" size={40} style={{ marginBottom: '12px' }} />
          <h3 style={{ margin: 0, color: 'var(--blue)', fontWeight: 700 }}>Loading Test Catalog...</h3>
        </div>
      </div>
    );
  }

  return (
    <div className="test-detail-page-wrapper" style={{ backgroundColor: '#f8fafc', minHeight: '100vh', fontFamily: 'var(--sans)' }}>
      <style>{`
        .lab-tests-layout {
          display: grid;
          grid-template-columns: 280px 1fr;
          gap: 30px;
          align-items: start;
        }
        .filter-sidebar {
          background-color: #ffffff;
          border: 1px solid var(--line);
          border-radius: 16px;
          padding: 20px;
          box-shadow: var(--shadow-sm);
        }
        .filter-section {
          border-bottom: 1px solid var(--line);
          padding-bottom: 16px;
          margin-bottom: 16px;
        }
        .filter-section:last-child {
          border-bottom: none;
          padding-bottom: 0;
          margin-bottom: 0;
        }
        .filter-section-header {
          display: flex;
          justify-content: space-between;
          align-items: center;
          width: 100%;
          border: none;
          background: none;
          font-weight: 700;
          color: var(--blue);
          font-size: 1rem;
          padding: 0;
          cursor: pointer;
          margin-bottom: 12px;
        }
        .view-type-toggle-bar {
          display: flex;
          background-color: #e2edf6;
          border-radius: 8px;
          padding: 4px;
          margin-bottom: 24px;
          width: max-content;
        }
        .view-type-toggle-btn {
          flex: 1;
          border: none;
          background: none;
          padding: 10px 24px;
          border-radius: 6px;
          font-weight: 700;
          font-size: 1.05rem;
          color: var(--muted);
          cursor: pointer;
          transition: all 0.2s;
          white-space: nowrap;
        }
        .view-type-toggle-btn.active {
          background-color: #ffffff;
          color: var(--blue);
          box-shadow: var(--shadow-sm);
        }
        .lab-tests-grid {
          display: grid;
          grid-template-columns: repeat(auto-fill, minmax(min(280px, 100%), 1fr));
          gap: 20px;
        }
        @media (max-width: 580px) {
          .view-type-toggle-bar {
            width: 100% !important;
            display: flex !important;
            box-sizing: border-box !important;
          }
          .view-type-toggle-btn {
            flex: 1 !important;
            font-size: 0.8rem !important;
            padding: 8px 4px !important;
            text-align: center !important;
          }
        }
        .lab-test-card {
          background: linear-gradient(135deg, var(--blue-soft) 0%, #e3f0fc 100%);
          border: 1.5px solid var(--line);
          border-radius: 16px;
          padding: 20px;
          box-shadow: var(--shadow-sm);
          display: flex;
          flex-direction: column;
          justify-content: space-between;
          min-height: 200px;
          transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
          cursor: default;
        }
        .lab-test-card:hover {
          transform: translateY(-2px);
          background: #ffffff;
          box-shadow: var(--shadow-lg);
          border-color: var(--teal);
        }
        .discount-badge {
          background-color: var(--orange);
          color: #ffffff;
          font-size: 0.75rem;
          font-weight: 800;
          padding: 4px 8px;
          border-radius: 5px;
          align-self: flex-start;
          margin-bottom: 10px;
        }
        .category-pill {
          background-color: var(--teal-soft);
          color: var(--teal-dark);
          font-size: 0.75rem;
          font-weight: 700;
          padding: 4px 8px;
          border-radius: 12px;
          align-self: flex-start;
          margin-bottom: 10px;
        }
        @media (max-width: 992px) {
          .lab-tests-layout {
            grid-template-columns: 1fr !important;
          }
        }
      `}</style>

      <div className="breadcrumbs-container">
        <div className="ribbon-breadcrumbs">
          <a href="#/" className="ribbon-breadcrumb-item">HOME</a>
          <div className="ribbon-breadcrumb-item active">CATALOG</div>
        </div>
      </div>

      <div className="page-section-container">
        
        {/* Header Section */}
        {/* Header Section */}
        <div style={{ marginBottom: '32px', textAlign: 'left' }}>
          <h1 style={{ fontSize: '2.4rem', fontWeight: 800, color: 'var(--blue)', margin: 0 }}>
            Book Lab Tests & Health Packages
          </h1>
          <p style={{ color: 'var(--muted)', fontSize: '1.1rem', marginTop: '8px', margin: '8px 0 0 0' }}>
            Select from our curated list of tests and body checkups with home sample collection.
          </p>
        </div>

        {/* Layout */}
        <div className="lab-tests-layout">
          
          {/* Left Panel: Sidebar Filters */}
          <aside className="filter-sidebar" style={{ textAlign: 'left' }}>
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '20px', borderBottom: '2px solid var(--line)', paddingBottom: '10px' }}>
              <span style={{ fontWeight: '800', color: 'var(--blue)', fontSize: '1.1rem', display: 'flex', alignItems: 'center', gap: '6px' }}>
                <Filter size={18} /> Filters
              </span>
              <button 
                onClick={() => {
                  setSelectedCategories([]);
                  setSortBy('popularity');
                }}
                style={{ border: 'none', background: 'none', color: 'var(--teal)', fontWeight: '700', fontSize: '0.85rem', cursor: 'pointer', padding: 0 }}
              >
                Clear All
              </button>
            </div>

            {/* Sort Section */}
            <div className="filter-section">
              <button className="filter-section-header" onClick={() => setIsSortOpen(!isSortOpen)}>
                <span style={{ display: 'flex', alignItems: 'center', gap: '6px' }}><ArrowUpDown size={16} /> Sort By</span>
                {isSortOpen ? <ChevronUp size={16} /> : <ChevronDown size={16} />}
              </button>
              
              {isSortOpen && (
                <div style={{ display: 'flex', flexDirection: 'column', gap: '10px', marginTop: '10px' }}>
                  {[
                    { label: 'Popularity', value: 'popularity' },
                    { label: 'Price: Low to High', value: 'low-to-high' },
                    { label: 'Price: High to Low', value: 'high-to-low' }
                  ].map((opt) => (
                    <label key={opt.value} style={{ display: 'flex', alignItems: 'center', gap: '8px', cursor: 'pointer', fontSize: '0.9rem', color: '#475569' }}>
                      <input 
                        type="radio" 
                        name="sort" 
                        value={opt.value} 
                        checked={sortBy === opt.value}
                        onChange={() => setSortBy(opt.value)}
                        style={{ accentColor: 'var(--teal)' }}
                      />
                      <span>{opt.label}</span>
                    </label>
                  ))}
                </div>
              )}
            </div>

            {/* Categories Section */}
            <div className="filter-section">
              <button className="filter-section-header" onClick={() => setIsCategoryOpen(!isCategoryOpen)}>
                <span style={{ display: 'flex', alignItems: 'center', gap: '6px' }}><BookOpen size={16} /> Categories</span>
                {isCategoryOpen ? <ChevronUp size={16} /> : <ChevronDown size={16} />}
              </button>
              
              {isCategoryOpen && (
                <div style={{ display: 'flex', flexDirection: 'column', gap: '10px', marginTop: '10px', maxHeight: '250px', overflowY: 'auto', paddingRight: '6px' }}>
                  {categoriesList.map((cat) => (
                    <label key={cat} style={{ display: 'flex', alignItems: 'center', gap: '8px', cursor: 'pointer', fontSize: '0.9rem', color: '#475569' }}>
                      <input 
                        type="checkbox" 
                        checked={selectedCategories.includes(cat)}
                        onChange={() => handleCategoryCheckboxChange(cat)}
                        style={{ accentColor: 'var(--teal)' }}
                      />
                      <span>{cat}</span>
                    </label>
                  ))}
                </div>
              )}
            </div>
          </aside>

          {/* Right Panel: Content Grid */}
          <main style={{ textAlign: 'left' }}>
            
            {/* View Type Toggle and Search Bar row */}
            <div style={{ display: 'flex', alignItems: 'center', gap: '20px', marginBottom: '24px', width: '100%', flexWrap: 'wrap' }}>
              {/* View Type Toggle */}
              <div className="view-type-toggle-bar" style={{ marginBottom: 0 }}>
                <button 
                  className={`view-type-toggle-btn ${viewType === 'all' ? 'active' : ''}`}
                  onClick={() => setViewType('all')}
                >
                  All
                </button>
                <button 
                  className={`view-type-toggle-btn ${viewType === 'test' ? 'active' : ''}`}
                  onClick={() => setViewType('test')}
                >
                  Individual Tests
                </button>
                <button 
                  className={`view-type-toggle-btn ${viewType === 'package' ? 'active' : ''}`}
                  onClick={() => setViewType('package')}
                >
                  Health Packages
                </button>
              </div>

              {/* Inner Search bar */}
              <div style={{ position: 'relative', flex: 1, minWidth: 'min(280px, 100%)' }}>
                <Search style={{ position: 'absolute', left: '12px', top: '50%', transform: 'translateY(-50%)', color: 'var(--muted)' }} size={18} />
                <input 
                  type="text" 
                  placeholder="Search tests or packages..." 
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  style={{
                    width: '100%',
                    padding: '10px 12px 10px 38px',
                    border: '1px solid var(--line)',
                    borderRadius: '8px',
                    outline: 'none',
                    fontSize: '0.95rem',
                    boxSizing: 'border-box'
                  }}
                />
              </div>
            </div>

            {/* Result Stats */}
            <div style={{ marginBottom: '20px', color: 'var(--muted)', fontSize: '0.95rem', fontWeight: '500' }}>
              Showing {filteredItems.length} results
            </div>

            {/* Cards Grid */}
            <div className="lab-tests-grid">
              {isLoading ? (
                // Shimmer Skeleton Loader Grid
                Array.from({ length: 6 }).map((_, idx) => (
                  <div key={idx} className="skeleton-card">
                    <div className="skeleton-header">
                      <div className="skeleton-badge shimmer-bg"></div>
                      <div className="skeleton-discount shimmer-bg"></div>
                    </div>
                    <div className="skeleton-title shimmer-bg"></div>
                    <div className="skeleton-subtitle shimmer-bg"></div>
                    <div className="skeleton-meta-row">
                      <div className="skeleton-meta-item shimmer-bg"></div>
                      <div className="skeleton-meta-item shimmer-bg"></div>
                    </div>
                    <div className="skeleton-footer">
                      <div className="skeleton-price-group">
                        <div className="skeleton-price-strike shimmer-bg"></div>
                        <div className="skeleton-price-active shimmer-bg"></div>
                      </div>
                      <div className="skeleton-btn shimmer-bg"></div>
                    </div>
                  </div>
                ))
              ) : filteredItems.length > 0 ? (
                filteredItems.map((item) => {
                  const isAdded = addedItems.includes(item.id);
                  return (
                    <div key={item.id} className="lab-test-card premium-tilt-card">
                      <div>
                        <span 
                          className="category-pill" 
                          style={{ 
                            backgroundColor: item.type === 'package' ? '#fff7ed' : '#e0f2fe', 
                            color: item.type === 'package' ? '#c2410c' : '#0369a1',
                            fontWeight: '700',
                            fontSize: '0.75rem',
                            padding: '4px 10px',
                            borderRadius: '6px',
                            display: 'inline-block',
                            marginBottom: '10px'
                          }}
                        >
                          {item.category || (item.type === 'package' ? 'Full Body Health' : 'Pathology')}
                        </span>
                        <h3 style={{ fontSize: '1.15rem', fontWeight: '800', margin: '0 0 8px 0', minHeight: '44px', lineHeight: '1.4' }}>
                          <a 
                            href={item.type === 'package' ? `#/package/${item.slug || (item.name || '').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '')}` : `#/test/${item.slug || (item.name || '').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '')}`}
                            style={{ color: 'var(--blue)', textDecoration: 'none', transition: 'color 0.2s' }}
                            onMouseEnter={(e) => e.target.style.color = 'var(--teal)'}
                            onMouseLeave={(e) => e.target.style.color = 'var(--blue)'}
                          >
                            {item.name}
                          </a>
                        </h3>
                        {item.type === 'package' && (
                          <p style={{ color: 'var(--muted)', fontSize: '0.85rem', margin: '0 0 12px 0' }}>
                            Includes {item.testsIncluded || item.testsCount || 60} tests
                          </p>
                        )}
                      </div>

                      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginTop: '16px', borderTop: '1px solid var(--line)', paddingTop: '16px', gap: '8px' }}>
                        <div style={{ flexShrink: 0 }}>
                          {item.originalPrice && item.originalPrice > item.price && (
                            <span style={{ fontSize: '0.85rem', textDecoration: 'line-through', color: 'var(--muted)', display: 'block', whiteSpace: 'nowrap' }}>
                              ₹ {item.originalPrice}
                            </span>
                          )}
                          <span style={{ fontSize: '1.25rem', fontWeight: '800', color: 'var(--teal)', whiteSpace: 'nowrap' }}>
                            ₹ {item.price}
                          </span>
                        </div>

                        <div style={{ display: 'flex', alignItems: 'center', gap: '12px', flexShrink: 0 }}>
                          <button
                            onClick={() => handleItemToggle(item.id)}
                            style={{
                              backgroundColor: isAdded ? '#fff3e0' : 'var(--orange)',
                              color: isAdded ? 'var(--orange-dark)' : '#ffffff',
                              border: isAdded ? '1px solid var(--orange)' : 'none',
                              borderRadius: '8px',
                              padding: '8px 16px',
                              fontWeight: '700',
                              fontSize: '0.85rem',
                              cursor: 'pointer',
                              transition: 'all 0.2s',
                              display: 'flex',
                              alignItems: 'center',
                              gap: '4px',
                              whiteSpace: 'nowrap'
                            }}
                          >
                            {isAdded ? (
                              <>
                                <Check size={14} /> ADDED
                              </>
                            ) : (
                              item.type === 'package' ? 'BOOK NOW' : 'ADD'
                            )}
                          </button>
                        </div>
                      </div>
                    </div>
                  );
                })
              ) : (
                <div style={{ gridColumn: '1 / -1', backgroundColor: '#ffffff', border: '1px solid var(--line)', borderRadius: '16px', padding: '60px 24px', textAlign: 'center', color: 'var(--muted)' }}>
                  No tests or packages found matching your criteria.
                </div>
              )}
            </div>

          </main>

        </div>
      </div>
    </div>
  );
};

export default LabTestsPage;
