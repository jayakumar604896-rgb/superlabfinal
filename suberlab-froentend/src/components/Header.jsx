import { ChevronDown, MapPin, Menu, Phone, Search, ShoppingCart, User, X, Download, ExternalLink } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';
import { FaWhatsapp } from 'react-icons/fa';

// import AnimatedLogo from './AnimatedLogo';
// import AnimatedLogo3 from './AnimatedLogo3';
// import StaticLogo from './StaticLogo';
import { fetchServices, fetchCategories, fetchPackages, registerCustomer, loginCustomer, getSuperlabCustomer, setSuperlabCustomer, logoutSuperlabCustomer } from '../services/api';
import { buildServiceNavItem, getPackageDetailHash, getServiceDetailHash } from '../utils/catalogLinks';
import DownloadReportModal from './DownloadReportModal';

const setLocationHash = (hash) => {
  window.location.hash = hash;
};



const Header = ({ isIsoModalOpen, setIsIsoModalOpen }) => {
  const [selectedLocation, setSelectedLocation] = useState('Chennai');
  const [searchQuery, setSearchQuery] = useState('');
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
  const [isCartAnimating, setIsCartAnimating] = useState(false);
  const [isMegaMenuOpen, setIsMegaMenuOpen] = useState(false);
  const [isCheckupMenuOpen, setIsCheckupMenuOpen] = useState(false);
  const [activeCategory, setActiveCategory] = useState('');
  const [isLoginOpen, setIsLoginOpen] = useState(false);
  const [isReportModalOpen, setIsReportModalOpen] = useState(false);
  const [authMode, setAuthMode] = useState('login');
  const [currentCustomer, setCurrentCustomer] = useState(() => getSuperlabCustomer());
  const [loginMobile, setLoginMobile] = useState('');
  const [loginPassword, setLoginPassword] = useState('');
  const [registerName, setRegisterName] = useState('');
  const [registerPassword, setRegisterPassword] = useState('');
  const [registerPasswordConfirm, setRegisterPasswordConfirm] = useState('');

  const authInputStyle = {
    border: '1.5px solid #cbd5e1',
    borderRadius: '8px',
    padding: '10px 14px',
    width: '100%',
    boxSizing: 'border-box',
    fontWeight: '600',
    fontSize: '0.95rem',
  };

  const resetAuthForms = () => {
    setLoginMobile('');
    setLoginPassword('');
    setRegisterName('');
    setRegisterEmail('');
    setRegisterMobile('');
    setRegisterAge('');
    setRegisterGender('Male');
    setRegisterBloodGroup('Unknown');
    setRegisterPassword('');
    setRegisterPasswordConfirm('');
  };

  useEffect(() => {
    const handleAuthUpdate = () => {
      setCurrentCustomer(getSuperlabCustomer());
    };
    window.addEventListener('superlab_auth_update', handleAuthUpdate);
    return () => window.removeEventListener('superlab_auth_update', handleAuthUpdate);
  }, []);

  useEffect(() => {
    const openReportModal = () => setIsReportModalOpen(true);
    window.addEventListener('superlab_open_report_modal', openReportModal);
    return () => window.removeEventListener('superlab_open_report_modal', openReportModal);
  }, []);

  useEffect(() => {
    const openLogin = () => {
      setIsLoginOpen(true);
      setAuthMode('login');
    };
    window.addEventListener('superlab_open_login', openLogin);
    return () => window.removeEventListener('superlab_open_login', openLogin);
  }, []);

  useEffect(() => {
    resetAuthForms();
  }, [isLoginOpen, authMode]);
  const [registerEmail, setRegisterEmail] = useState('');
  const [registerMobile, setRegisterMobile] = useState('');
  const [registerAge, setRegisterAge] = useState('');
  const [registerGender, setRegisterGender] = useState('Male');
  const [registerBloodGroup, setRegisterBloodGroup] = useState('Unknown');
  const [isIsoHovered, setIsIsoHovered] = useState(false);
  const [isMobileFindTestOpen, setIsMobileFindTestOpen] = useState(false);
  const [isMobileCheckupOpen, setIsMobileCheckupOpen] = useState(false);
  const [mobileActiveCategory, setMobileActiveCategory] = useState(null);
  const [headerPackages, setHeaderPackages] = useState([]);
  const [searchCatalog, setSearchCatalog] = useState([]);
  const [categoryData, setCategoryData] = useState({});
  const [isCatalogLoading, setIsCatalogLoading] = useState(true);

  const [cartCount, setCartCount] = useState(() => {
    return window.getSuperlabCart ? window.getSuperlabCart().length : 0;
  });

  const [isStickyCart, setIsStickyCart] = useState(false);

  useEffect(() => {
    const handleScroll = () => {
      if (window.scrollY > 80) {
        setIsStickyCart(true);
      } else {
        setIsStickyCart(false);
      }
    };
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  useEffect(() => {
    const handleCartUpdate = () => {
      if (window.getSuperlabCart) {
        setCartCount(window.getSuperlabCart().length);
      }
    };
    window.addEventListener('superlab_cart_update', handleCartUpdate);
    // Sync initial state
    handleCartUpdate();
    return () => window.removeEventListener('superlab_cart_update', handleCartUpdate);
  }, []);

  const isInitialMount = useRef(true);
  useEffect(() => {
    if (isInitialMount.current) {
      isInitialMount.current = false;
      return;
    }
    setIsCartAnimating(true);
    const timer = setTimeout(() => setIsCartAnimating(false), 500);
    return () => clearTimeout(timer);
  }, [cartCount]);

  useEffect(() => {
    if (isMobileMenuOpen) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = '';
    }
    return () => {
      document.body.style.overflow = '';
    };
  }, [isMobileMenuOpen]);

  useEffect(() => {
    const handleKeyDown = (e) => {
      if (e.key === 'Escape') {
        setIsIsoModalOpen(false);
      }
    };
    if (isIsoModalOpen) {
      window.addEventListener('keydown', handleKeyDown);
    }
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, [isIsoModalOpen, setIsIsoModalOpen]);

  const leaveTimeoutRef = useRef(null);
  const checkupTimeoutRef = useRef(null);

  const locations = [
    'Chennai',
    'New Delhi',
    'Noida',
    'Gurugram',
    'Mumbai',
    'Bengaluru',
    'Kolkata',
    'Hyderabad'
  ];

  useEffect(() => {
    Promise.all([fetchCategories(), fetchServices(), fetchPackages()]).then(([{ data: cats }, { data: services }, { data: pkgs }]) => {
      if ((cats && cats.length > 0) || (services && services.length > 0)) {
        const dynamicData = {};
        if (cats && cats.length > 0) {
          cats.forEach(c => {
            const cName = c.category_name || c.name;
            if (cName && !dynamicData[cName]) {
              dynamicData[cName] = { title: cName, tests: [] };
            }
          });
        }
        if (services && services.length > 0) {
          services.forEach(s => {
            let catName = 'General';
            if (s.category) {
              catName = typeof s.category === 'object' ? s.category.name : s.category;
            }
            if (!dynamicData[catName]) {
              dynamicData[catName] = { title: catName, tests: [] };
            }
            const navItem = buildServiceNavItem(s);
            if (!navItem.name || !navItem.slug) {
              return;
            }
            const exists = dynamicData[catName].tests.some((test) => test.slug === navItem.slug);
            if (!exists) {
              dynamicData[catName].tests.push(navItem);
            }
          });
        }
        setCategoryData(dynamicData);

        const keys = Object.keys(dynamicData);
        if (keys.length > 0 && !dynamicData[activeCategory]) {
          setActiveCategory(keys[0]);
        }
      }

      if (pkgs && pkgs.length > 0) {
        setHeaderPackages(pkgs);
      }

      const catalogItems = [];
      if (services && services.length > 0) {
        services.forEach((s) => {
          catalogItems.push({
            id: s.id,
            name: s.name || s.title,
            slug: s.slug,
            hash: s.hash,
            type: 'test',
            category: typeof s.category === 'object' ? s.category?.name : s.category,
            price: s.price,
            original_price: s.original_price,
          });
        });
      }
      if (pkgs && pkgs.length > 0) {
        pkgs.forEach((p) => {
          catalogItems.push({
            id: p.id,
            name: p.name,
            slug: p.slug,
            hash: p.hash,
            type: 'package',
            price: p.offer_price,
            original_price: p.original_price,
          });
        });
      }
      if (catalogItems.length > 0) {
        setSearchCatalog(catalogItems);
      }
    }).finally(() => {
      setIsCatalogLoading(false);
    });
  }, []);

  const resolveCatalogHash = (item) => {
    if (!item) return '#/lab-tests';
    if (typeof item === 'string') {
      const match = searchCatalog.find((entry) => entry.name?.toLowerCase() === item.toLowerCase());
      if (match) {
        return match.type === 'package' ? getPackageDetailHash(match) : getServiceDetailHash(match);
      }
      return '#/lab-tests';
    }
    return item.hash || getServiceDetailHash(item);
  };

  const [showSuggestions, setShowSuggestions] = useState(false);
  const suggestionsRef = useRef(null);

  const searchDatabase = searchCatalog;

  useEffect(() => {
    const handleOutsideClick = (e) => {
      if (suggestionsRef.current && !suggestionsRef.current.contains(e.target)) {
        setShowSuggestions(false);
      }
    };
    document.addEventListener('mousedown', handleOutsideClick);
    return () => document.removeEventListener('mousedown', handleOutsideClick);
  }, []);

  const handleSearch = (e) => {
    e.preventDefault();
    if (searchQuery.trim() !== '') {
      setShowSuggestions(false);
      sessionStorage.setItem('superlab_search_query', searchQuery);
      setLocationHash('#/lab-tests');
      window.dispatchEvent(new Event('superlab_search_trigger'));
    }
  };

  const handleSuggestionClick = (item) => {
    setSearchQuery(item.name);
    setShowSuggestions(false);
    if (item.slug) {
      setLocationHash(item.type === 'package' ? getPackageDetailHash(item) : getServiceDetailHash(item));
      return;
    }
    setLocationHash('#/lab-tests');
  };

  const handleMouseEnter = () => {
    if (leaveTimeoutRef.current) clearTimeout(leaveTimeoutRef.current);
    setIsMegaMenuOpen(true);
  };

  const handleMouseLeave = () => {
    leaveTimeoutRef.current = setTimeout(() => {
      setIsMegaMenuOpen(false);
    }, 200);
  };

  const handleCheckupEnter = () => {
    if (checkupTimeoutRef.current) clearTimeout(checkupTimeoutRef.current);
    setIsCheckupMenuOpen(true);
  };

  const handleCheckupLeave = () => {
    checkupTimeoutRef.current = setTimeout(() => {
      setIsCheckupMenuOpen(false);
    }, 200);
  };

  const renderUtilities = (additionalClassName = '') => (
    <div className={`header-utilities ${additionalClassName}`} style={{ display: 'flex', alignItems: 'center', gap: 'clamp(12px, 2vw, 24px)' }}>
      {/* Location Dropdown */}
      <div className="header-location-picker" style={{ display: 'flex', alignItems: 'center', gap: '4px', color: '#003c71' }}>
        <MapPin size={18} style={{ color: '#00a3ad' }} />
        <select
          value={selectedLocation}
          onChange={(e) => setSelectedLocation(e.target.value)}
          style={{
            border: 'none',
            background: 'transparent',
            fontWeight: 'bold',
            color: '#003c71',
            cursor: 'pointer',
            outline: 'none',
            paddingRight: '4px'
          }}
        >
          {locations.map((loc) => (
            <option key={loc} value={loc}>{loc}</option>
          ))}
        </select>
      </div>

      {/* User Account Circular Icon */}
      {(() => {
        const currentCustomer = (() => {
          try {
            const raw = localStorage.getItem('superlab_customer');
            return raw ? JSON.parse(raw) : null;
          } catch (e) {
            return null;
          }
        })();

        return (
          <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
            {currentCustomer && (
              <span
                onClick={() => { window.location.hash = '#/profile'; }}
                style={{ fontSize: '0.85rem', fontWeight: '700', color: 'var(--blue)', cursor: 'pointer' }}
              >
                Hi, {currentCustomer.name ? currentCustomer.name.split(' ')[0] : 'Patient'}
              </span>
            )}
            <button
              title={currentCustomer ? currentCustomer.name : "User Account"}
              onClick={() => {
                if (currentCustomer) {
                  window.location.hash = '#/profile';
                } else {
                  setIsLoginOpen(true);
                  setAuthMode('login');
                }
              }}
              style={{
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                width: '32px',
                height: '32px',
                borderRadius: '50%',
                border: '2px solid #00a3ad',
                color: '#00a3ad',
                background: currentCustomer ? '#e6f7f8' : 'transparent',
                cursor: 'pointer',
                padding: 0
              }}
            >
              <User size={18} />
            </button>
          </div>
        );
      })()}

      {/* Shopping Cart Icon */}
      <a
        href="#/cart"
        className={isCartAnimating ? 'cart-animate-trigger' : ''}
        title="Shopping Cart"
        style={isStickyCart ? {
          color: '#00a3ad',
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'center',
          position: 'fixed',
          top: '16px',
          right: '24px',
          zIndex: 99999,
          backgroundColor: '#ffffff',
          width: '42px',
          height: '42px',
          borderRadius: '50%',
          boxShadow: '0 4px 12px rgba(0, 163, 173, 0.25)',
          border: '1.5px solid #e2e8f0',
          transition: 'all 0.2s ease-in-out'
        } : {
          color: '#00a3ad',
          display: 'flex',
          alignItems: 'center',
          position: 'relative'
        }}
      >
        <ShoppingCart size={isStickyCart ? 20 : 24} />
        <span style={{
          position: 'absolute',
          top: isStickyCart ? '-2px' : '-8px',
          right: isStickyCart ? '-2px' : '-8px',
          backgroundColor: 'var(--orange)',
          color: 'white',
          borderRadius: '50%',
          width: '18px',
          height: '18px',
          fontSize: '0.7rem',
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'center',
          fontWeight: 'bold',
          border: isStickyCart ? '1.5px solid #ffffff' : 'none'
        }}>{cartCount}</span>
      </a>

      {/* Mobile Menu Icon */}
      <button className="menu-toggle" onClick={() => setIsMobileMenuOpen(true)}>
        <Menu size={24} />
      </button>
    </div>
  );

  const filteredSuggestions = searchQuery.trim() === ''
    ? []
    : searchDatabase.filter(item =>
      item.name.toLowerCase().includes(searchQuery.toLowerCase())
    );

  return (
    <header style={{ width: '100%', fontFamily: 'var(--sans)', position: 'relative', zIndex: 1000 }}>
      <style>{`
        .superlab-header-logo-link {
          display: flex;
          align-items: center;
          height: 42px;
          position: relative;
          z-index: 2;
        }

        .superlab-header-logo {
          display: block;
          width: auto;
          height: 71px;
          max-width: min(400px, 45vw);
          animation: superlabHeartbeat 3s infinite ease-in-out;
          transform-origin: center;
          will-change: transform;
        }

        @keyframes superlabHeartbeat {
          0%, 30%, 100% { transform: scale(1); }
          10% { transform: scale(1.035); }
          20% { transform: scale(1.015); }
          25% { transform: scale(1.04); }
        }

        @media (max-width: 768px) {
          .superlab-header-logo-link {
            height: 38px;
          }
          .superlab-header-logo {
            height: 65px;
            max-width: 320px;
          }
        }

        @media (max-width: 480px) {
          .superlab-header-logo-link {
            height: 32px;
          }
          .superlab-header-logo {
            height: 54px;
            max-width: 270px;
          }
        }

        @media (prefers-reduced-motion: reduce) {
          .superlab-header-logo {
            animation: none;
          }
        }
      `}</style>
      {/* 0. MOBILE ONLY TOP CONTACT BAR */}
      <div className="mobile-top-contact-bar">
        <a href="tel:+918939905115" className="mobile-top-contact-btn mobile-call-btn">
          <Phone size={14} fill="currentColor" />
          <span>+91 8939905115</span>
        </a>
        <a href="https://wa.me/918939905115" target="_blank" rel="noopener noreferrer" className="mobile-top-contact-btn mobile-wa-btn">
          <FaWhatsapp size={16} />
          <span>WhatsApp</span>
        </a>
      </div>

      {/* 1. TOP ROW: Brand Soft Background */}
      <div className="header-top-bar">
        {/* Top Left: Animated Logo Block & ISO badge */}
        <div style={{ display: 'flex', alignItems: 'center', gap: '16px' }}>
          <a href="#/" className="superlab-header-logo-link">
            <img
              src="/superlab-logo-transparent.png"
              alt="SuperLab by Phlebee"
              className="superlab-header-logo"
            />
            {/* <StaticLogo height={42} /> */}
          </a>
          <div
            onClick={() => setIsIsoModalOpen(true)}
            onMouseEnter={() => setIsIsoHovered(true)}
            onMouseLeave={() => setIsIsoHovered(false)}
            style={{
              marginLeft: 'clamp(10px, 2vw, 30px)',
              display: 'flex',
              alignItems: 'center',
              gap: '6px',
              backgroundColor: isIsoHovered ? '#fef3c7' : '#fffbeb',
              color: '#b45309',
              padding: '6px 12px',
              borderRadius: '20px',
              fontSize: '0.8rem',
              fontWeight: 'bold',
              border: isIsoHovered ? '1px solid #f59e0b' : '1px solid #fde68a',
              whiteSpace: 'nowrap',
              cursor: 'pointer',
              transform: isIsoHovered ? 'translateY(-1px) scale(1.02)' : 'translateY(0) scale(1)',
              boxShadow: isIsoHovered ? '0 4px 10px rgba(217, 119, 6, 0.15)' : 'none',
              transition: 'all 0.2s ease-in-out'
            }}
            className="header-iso-badge"
          >
            <img
              src="/iso-logo.png"
              alt="ISO Logo"
              style={{ width: '16px', height: '16px', borderRadius: '2px', objectFit: 'contain', marginRight: '4px', flexShrink: 0 }}
            />
            <span>ISO-15189:2022  Certified</span>
          </div>
        </div>

        {/* Top Center: Long Search Bar with Search Icon inside on the Right */}
        <form onSubmit={handleSearch} className="header-top-search" style={{ position: 'relative' }} ref={suggestionsRef}>
          <div className="header-search-wrapper">
            <input
              type="text"
              placeholder="Search for tests, packages, symptoms..."
              value={searchQuery}
              onChange={(e) => {
                setSearchQuery(e.target.value);
                setShowSuggestions(true);
              }}
              onFocus={() => setShowSuggestions(true)}
              className="header-search-input"
            />
            <button type="submit" className="header-search-btn">
              <Search size={18} />
            </button>
          </div>

          {showSuggestions && filteredSuggestions.length > 0 && (
            <div style={{
              position: 'absolute',
              top: '100%',
              left: '0',
              right: '0',
              backgroundColor: '#ffffff',
              borderRadius: '12px',
              boxShadow: '0 8px 30px rgba(0, 60, 113, 0.12)',
              border: '1px solid #e2edf6',
              zIndex: 1050,
              marginTop: '8px',
              maxHeight: '320px',
              overflowY: 'auto',
              padding: '6px 0'
            }}>
              {filteredSuggestions.map((item, idx) => (
                <div
                  key={idx}
                  onClick={() => handleSuggestionClick(item)}
                  style={{
                    padding: '12px 18px',
                    cursor: 'pointer',
                    display: 'flex',
                    justifyContent: 'space-between',
                    alignItems: 'center',
                    borderBottom: idx < filteredSuggestions.length - 1 ? '1px solid #f1f5f9' : 'none',
                    transition: 'background-color 0.2s',
                    textAlign: 'left'
                  }}
                  className="search-suggestion-item"
                >
                  <div style={{ display: 'flex', flexDirection: 'column', gap: '2px' }}>
                    <span style={{ fontSize: '0.95rem', fontWeight: '600', color: '#003c71' }}>
                      {item.name}
                    </span>
                    <span style={{ fontSize: '0.75rem', fontWeight: 'bold', color: item.type === 'package' ? '#ff6b00' : '#00a3ad', textTransform: 'uppercase', letterSpacing: '0.5px' }}>
                      {item.type}
                    </span>
                  </div>
                  <span style={{ fontSize: '0.9rem', fontWeight: '800', color: '#003c71' }}>
                    ₹{item.price}
                  </span>
                </div>
              ))}
            </div>
          )}
        </form>

        {/* Top Right: Book a Test Action Button */}
        <div>
          <button
            onClick={() => { setLocationHash('#/lab-tests'); }}
            className="btn-book-test-header"
          >
            Book a Test
          </button>
        </div>

        {renderUtilities('mobile-only-utilities')}
      </div>

      {/* 2. BOTTOM ROW: White Background */}
      <div className="header-bottom-row" style={{
        backgroundColor: '#ffffff',
        borderBottom: '1px solid #e2e8f0',
        padding: '0 clamp(12px, 2vw, 24px)',
        height: '60px',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'space-between',
        position: 'relative',
        zIndex: 5
      }}>
        {/* Bottom Left: WhatsApp & Phone */}
        <div style={{ display: 'flex', alignItems: 'center', gap: 'clamp(10px, 1.8vw, 20px)' }} className="header-bottom-contacts">
          <a href="https://wa.me/91 8939905115" target="_blank" rel="noopener noreferrer" style={{ display: 'flex', alignItems: 'center' }}>
            <FaWhatsapp size={22} style={{ color: '#25D366' }} />
          </a>
          <span style={{ height: '16px', width: '1px', backgroundColor: 'var(--line)' }}></span>
          <a href="tel:+91 8939905115" style={{ display: 'flex', alignItems: 'center', gap: '8px', color: 'var(--blue)', fontWeight: 'bold', fontSize: '1.05rem', textDecoration: 'none' }}>
            <Phone size={18} fill="var(--blue)" style={{ color: 'var(--blue)' }} />
            <span>+91  8939905115</span>
          </a>
        </div>

        {/* Bottom Center: Navigation Links */}
        <nav className="nav-menu">
          {/* Find A Test Wrapper with Hover Handler */}
          <div
            onMouseEnter={handleMouseEnter}
            onMouseLeave={handleMouseLeave}
            style={{
              display: 'inline-block',
              paddingBottom: '20px',
              marginBottom: '-20px'
            }}
          >
            <a
              href="#find-test"
              className="nav-item"
              style={{
                fontSize: '1rem',
                fontWeight: 'bold',
                color: '#003c71',
                textDecoration: 'none',
                padding: '12px 0',
                display: 'inline-block'
              }}
            >
              Find A Test
            </a>

            {/* MEGA MENU CONTAINER - Nested inside the hover-trigger wrapper */}
            <div
              className={`mega-menu-container ${isMegaMenuOpen ? 'open' : ''}`}
              onMouseEnter={handleMouseEnter}
              onMouseLeave={handleMouseLeave}
            >
              <div className="mega-menu-content" style={{ display: 'flex', minHeight: '380px' }}>
                {/* Left Column: Categories List */}
                <div className="mega-menu-left" style={{ width: '260px', backgroundColor: '#f8fafc', padding: '16px', display: 'flex', flexDirection: 'column', gap: '6px', borderRight: '1px solid #f1f5f9' }}>
                  {isCatalogLoading ? (
                    <div style={{ padding: '12px', color: '#64748b', fontSize: '0.9rem' }}>Loading catalog...</div>
                  ) : Object.keys(categoryData).length === 0 ? (
                    <div style={{ padding: '12px', color: '#64748b', fontSize: '0.9rem' }}>Catalog unavailable. Connect to the API to browse tests.</div>
                  ) : (
                    Object.keys(categoryData).map((cat) => (
                      <button
                        key={cat}
                        className={`mega-menu-category-btn ${activeCategory === cat ? 'active' : ''}`}
                        onMouseEnter={() => setActiveCategory(cat)}
                        style={{
                          display: 'flex',
                          justifyContent: 'space-between',
                          alignItems: 'center',
                          width: '100%',
                          padding: '10px 16px',
                          border: 'none',
                          background: 'none',
                          borderRadius: '6px',
                          fontSize: '0.95rem',
                          fontWeight: '600',
                          color: activeCategory === cat ? '#00a3ad' : '#475569',
                          backgroundColor: activeCategory === cat ? '#e6f7f8' : 'transparent',
                          textAlign: 'left',
                          cursor: 'pointer',
                          transition: 'all 0.2s'
                        }}
                      >
                        <span>{cat}</span>
                        <ChevronDown size={14} style={{ transform: 'rotate(-90deg)' }} />
                      </button>
                    ))
                  )}

                  <a href="#/lab-tests" onClick={() => setIsMegaMenuOpen(false)} className="mega-menu-test-link" style={{
                    color: '#003c71',
                    fontWeight: 'bold',
                    marginTop: 'auto',
                    padding: '10px 16px',
                    display: 'inline-block',
                    fontSize: '0.9rem',
                    textDecoration: 'none'
                  }}>
                    View All Categories
                  </a>
                </div>

                {/* Vertical Divider */}
                <div className="mega-menu-divider" style={{ width: '1.5px', backgroundColor: '#003c71', opacity: 0.15, alignSelf: 'stretch', margin: '20px 0' }} />

                {/* Right Column: Tests List Grid & Features */}
                <div className="mega-menu-right" style={{ flex: '1', padding: '24px 24px 16px 24px', display: 'flex', flexDirection: 'column', height: '100%', boxSizing: 'border-box' }}>
                  <h3 className="mega-menu-title" style={{ fontSize: '1.3rem', fontWeight: 'bold', color: '#00a3ad', marginBottom: '16px', textAlign: 'left', flexShrink: 0 }}>
                    {categoryData[activeCategory]?.title || activeCategory}
                  </h3>

                  {/* Scrollable Tests Grid */}
                  <div className="mega-menu-tests-scroll-wrapper" style={{ flex: '1', overflowY: 'auto', paddingRight: '10px', marginBottom: '16px' }}>
                    <div className="mega-menu-grid" style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: '12px 24px', textAlign: 'left' }}>
                      {categoryData[activeCategory]?.tests?.map((test) => (
                        <a
                          key={test.slug || test.name}
                          href={resolveCatalogHash(test)}
                          onClick={() => setIsMegaMenuOpen(false)}
                          className="mega-menu-test-link"
                          style={{ fontSize: '0.9rem', color: '#334155', textDecoration: 'none', transition: 'color 0.15s', cursor: 'pointer', lineHeight: '1.4' }}
                        >
                          {test.name}
                        </a>
                      ))}
                    </div>
                  </div>

                  {/* Divider line */}
                  <div style={{ height: '1.5px', backgroundColor: '#f1f5f9', width: '100%', flexShrink: 0, marginBottom: '16px' }} />

                  {/* Features Grid */}
                  <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '16px', flexShrink: 0 }}>
                    {/* Item 1 */}
                    <div style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
                      <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', width: '32px', height: '32px', borderRadius: '50%', backgroundColor: '#e2f5f6', color: '#00828a', flexShrink: 0 }}>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                          <path d="M19 15c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3zm-14 0c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3zm15-6h-2.18L16 5.86V3h-2v2.86l-2.18 3.14H5c-1.1 0-2 .9-2 2v3h18v-3c0-1.1-.9-2-2-2z" />
                        </svg>
                      </div>
                      <div style={{ display: 'flex', flexDirection: 'column', textAlign: 'left', lineHeight: '1.15' }}>
                        <span style={{ fontSize: '0.8rem', fontWeight: '800', color: '#003c71' }}>Home Sample</span>
                        <span style={{ fontSize: '0.8rem', fontWeight: '800', color: '#003c71' }}>Collection</span>
                      </div>
                    </div>

                    {/* Item 2 */}
                    <div style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
                      <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', width: '32px', height: '32px', borderRadius: '50%', backgroundColor: '#e2f5f6', color: '#00828a', flexShrink: 0 }}>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" /><polyline points="22,6 12,13 2,6" /></svg>
                      </div>
                      <div style={{ display: 'flex', flexDirection: 'column', textAlign: 'left', lineHeight: '1.15' }}>
                        <span style={{ fontSize: '0.8rem', fontWeight: '800', color: '#003c71' }}>Same Day</span>
                        <span style={{ fontSize: '0.8rem', fontWeight: '800', color: '#003c71' }}>Reports Over Email</span>
                      </div>
                    </div>

                    {/* Item 3 */}
                    <div style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
                      <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', width: '32px', height: '32px', borderRadius: '50%', backgroundColor: '#e2f5f6', color: '#00828a', flexShrink: 0 }}>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" /><circle cx="12" cy="7" r="4" /></svg>
                      </div>
                      <div style={{ display: 'flex', flexDirection: 'column', textAlign: 'left', lineHeight: '1.15' }}>
                        <span style={{ fontSize: '0.8rem', fontWeight: '800', color: '#003c71' }}>Highly Trained</span>
                        <span style={{ fontSize: '0.8rem', fontWeight: '800', color: '#003c71' }}>Staff & Doctors</span>
                      </div>
                    </div>

                    {/* Item 4 */}
                    <div style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
                      <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', width: '32px', height: '32px', borderRadius: '50%', backgroundColor: '#e2f5f6', color: '#00828a', flexShrink: 0 }}>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2" /><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" /></svg>
                      </div>
                      <div style={{ display: 'flex', flexDirection: 'column', textAlign: 'left', lineHeight: '1.15' }}>
                        <span style={{ fontSize: '0.8rem', fontWeight: '800', color: '#003c71' }}>Zero Contamination</span>
                        <span style={{ fontSize: '0.8rem', fontWeight: '800', color: '#003c71' }}>QualiCare Kit</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {/* Full Body Health Checkup Wrapper with Hover Handler */}
          <div
            onMouseEnter={handleCheckupEnter}
            onMouseLeave={handleCheckupLeave}
            style={{
              display: 'inline-block',
              paddingBottom: '20px',
              marginBottom: '-20px',
              position: 'relative'
            }}
          >
            <a
              href="#health-checkup"
              className="nav-item"
              style={{
                fontSize: '1rem',
                fontWeight: 'bold',
                color: '#003c71',
                textDecoration: 'none',
                padding: '12px 0',
                display: 'inline-block'
              }}
            >
              Full Body Health Checkup
            </a>

            {/* CHECKUP DROPDOWN CONTAINER */}
            <div className={`checkup-dropdown-container ${isCheckupMenuOpen ? 'open' : ''}`}>
              {headerPackages.map((pkg) => {
                const cost = pkg.offer_price ?? 1499;
                const orig = pkg.original_price;
                return (
                  <div key={pkg.id || pkg.name} className="checkup-package-row">
                    <div className="package-left">
                      <span className="package-name">{pkg.name}</span>
                      {pkg.badge && <span className="package-badge-green">{pkg.badge}</span>}
                    </div>
                    <span className="package-tests-count">Includes {pkg.tests_included_count || 90} Tests</span>
                    <button className="package-details-btn" onClick={() => { setLocationHash(getPackageDetailHash(pkg)); setIsCheckupMenuOpen(false); }}>See Details</button>
                    <button className="package-book-outline-btn" onClick={() => { window.addToSuperlabCart({ id: pkg.id, name: pkg.name, category: 'Full Body Health', type: 'package', price: cost, ...(orig ? { originalPrice: orig } : {}) }); setIsCheckupMenuOpen(false); }}>Book Now</button>
                    <div style={{ display: 'flex', alignItems: 'center', gap: '6px' }}>
                      {orig && <span style={{ textDecoration: 'line-through', color: '#94a3b8', fontSize: '0.82rem', fontWeight: '500' }}>₹{orig}</span>}
                      <span className="package-price-text">₹{cost}</span>
                    </div>
                  </div>
                );
              })}

              <button className="checkup-view-all-btn" onClick={() => { sessionStorage.setItem('superlab_view_type', 'package'); setLocationHash('#/lab-tests'); setIsCheckupMenuOpen(false); }}>
                VIEW ALL PACKAGES
              </button>
            </div>
          </div>

          <button
            type="button"
            className="nav-item"
            style={{ fontSize: '1rem', fontWeight: 'bold', color: '#003c71', background: 'none', border: 'none', cursor: 'pointer', padding: 0 }}
            onClick={() => setIsReportModalOpen(true)}
          >
            Download Report
          </button>
          <a href="#/make-package" className="nav-item" style={{ fontSize: '1rem', fontWeight: 'bold', color: '#003c71', textDecoration: 'none' }}>Make Your Own Package</a>
        </nav>

        {/* Bottom Right: Location Picker, User Account, Shopping Cart */}
        {renderUtilities('desktop-only-utilities')}
      </div>

      {/* Mobile Drawer Menu */}
      <div className={`mobile-nav-backdrop ${isMobileMenuOpen ? 'open' : ''}`} onClick={() => setIsMobileMenuOpen(false)} />
      <div className={`mobile-nav ${isMobileMenuOpen ? 'open' : ''}`}>
        <div className="mobile-nav-header">
          <span className="brand-name" style={{ color: '#003c71' }}>
            SUPER<span style={{ color: 'var(--orange)' }}>LAB</span>
          </span>
          <button className="close-btn" onClick={() => setIsMobileMenuOpen(false)}>
            <X size={24} />
          </button>
        </div>
        <div className="mobile-nav-links" style={{ display: 'flex', flexDirection: 'column', gap: '16px', marginTop: '20px' }}>
          <button
            onClick={() => { setLocationHash('#/lab-tests'); setIsMobileMenuOpen(false); }}
            className="btn-book-test-header btn-book-test-drawer"
            style={{ width: '100%', padding: '12px', fontSize: '1rem', textAlign: 'center', marginBottom: '8px' }}
          >
            Book a Test
          </button>
          {/* Find A Test Collapsible Accordion */}
          <div style={{ display: 'flex', flexDirection: 'column' }}>
            <button
              className="mobile-nav-item"
              onClick={() => setIsMobileFindTestOpen(!isMobileFindTestOpen)}
              style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', background: 'none', border: 'none', width: '100%', textAlign: 'left', cursor: 'pointer', font: 'inherit', color: 'inherit' }}
            >
              <span>Find A Test</span>
              <ChevronDown size={18} style={{ transform: isMobileFindTestOpen ? 'rotate(180deg)' : 'rotate(0)', transition: 'transform 0.2s' }} />
            </button>
            {isMobileFindTestOpen && (
              <div style={{ display: 'flex', flexDirection: 'column', gap: '8px', paddingLeft: '12px', marginTop: '8px', borderLeft: '2px solid var(--blue-soft)' }}>
                {Object.keys(categoryData).map((cat) => (
                  <div key={cat} style={{ display: 'flex', flexDirection: 'column' }}>
                    <button
                      onClick={() => setMobileActiveCategory(mobileActiveCategory === cat ? null : cat)}
                      style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', width: '100%', background: 'none', border: 'none', padding: '6px 8px', color: 'var(--blue)', cursor: 'pointer', textAlign: 'left', fontWeight: '600' }}
                    >
                      <span>{cat}</span>
                      <ChevronDown size={14} style={{ transform: mobileActiveCategory === cat ? 'rotate(180deg)' : 'rotate(0)', transition: 'transform 0.2s', color: 'var(--teal)' }} />
                    </button>
                    {mobileActiveCategory === cat && (
                      <div style={{ display: 'flex', flexDirection: 'column', gap: '4px', paddingLeft: '8px', borderLeft: '1.5px dashed var(--teal-soft)', marginTop: '4px', marginBottom: '6px' }}>
                        {categoryData[cat]?.tests?.map((test) => (
                          <a
                            key={test.slug || test.name}
                            href={resolveCatalogHash(test)}
                            onClick={() => {
                              setIsMobileMenuOpen(false);
                            }}
                            style={{ display: 'block', width: '100%', textAlign: 'left', color: '#475569', padding: '4px 8px', textDecoration: 'none', fontWeight: '500', fontSize: '0.85rem' }}
                          >
                            {test.name}
                          </a>
                        ))}
                      </div>
                    )}
                  </div>
                ))}
              </div>
            )}
          </div>

          {/* Full Body Health Checkup Collapsible Accordion */}
          <div style={{ display: 'flex', flexDirection: 'column' }}>
            <button
              className="mobile-nav-item"
              onClick={() => setIsMobileCheckupOpen(!isMobileCheckupOpen)}
              style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', background: 'none', border: 'none', width: '100%', textAlign: 'left', cursor: 'pointer', font: 'inherit', color: 'inherit' }}
            >
              <span>Full Body Health Checkup</span>
              <ChevronDown size={18} style={{ transform: isMobileCheckupOpen ? 'rotate(180deg)' : 'rotate(0)', transition: 'transform 0.2s' }} />
            </button>
            {isMobileCheckupOpen && (
              <div style={{ display: 'flex', flexDirection: 'column', gap: '8px', paddingLeft: '12px', marginTop: '8px', borderLeft: '2px solid var(--blue-soft)' }}>
                {headerPackages.map((pkg) => (
                  <button
                    key={pkg.id || pkg.name}
                    onClick={() => {
                      setLocationHash(getPackageDetailHash(pkg));
                      setIsMobileMenuOpen(false);
                    }}
                    style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', width: '100%', textAlign: 'left', background: 'none', border: 'none', color: '#475569', padding: '6px 8px', cursor: 'pointer', font: 'inherit', fontWeight: '500', fontSize: '0.88rem' }}
                  >
                    <span>{pkg.name}</span>
                    <span style={{ fontSize: '0.8rem', fontWeight: '700', color: '#003c71' }}>₹{pkg.offer_price || pkg.price}</span>
                  </button>
                ))}
                <button
                  onClick={() => { sessionStorage.setItem('superlab_view_type', 'package'); setLocationHash('#/lab-tests'); setIsMobileMenuOpen(false); }}
                  style={{ width: '100%', padding: '8px', border: '1px solid var(--blue)', color: 'var(--blue)', fontWeight: 'bold', backgroundColor: '#fff', borderRadius: '6px', cursor: 'pointer', textTransform: 'uppercase', letterSpacing: '0.5px', marginTop: '4px' }}
                >
                  View All Packages
                </button>
              </div>
            )}
          </div>
          <button
            type="button"
            className="mobile-nav-item"
            style={{ background: 'none', border: 'none', textAlign: 'left', cursor: 'pointer', font: 'inherit', color: 'inherit', padding: 0 }}
            onClick={() => { setIsReportModalOpen(true); setIsMobileMenuOpen(false); }}
          >
            Download Report
          </button>
          <a href="#/make-package" className="mobile-nav-item" onClick={() => setIsMobileMenuOpen(false)}>Make Your Own Package</a>

          <div className="drawer-utilities-section" style={{ borderTop: '1px solid var(--line)', paddingTop: '16px', marginTop: '8px' }}>
            <div className="drawer-location-picker" style={{ display: 'flex', alignItems: 'center', gap: '8px', padding: '8px 0' }}>
              <MapPin size={18} style={{ color: '#00a3ad' }} />
              <select
                value={selectedLocation}
                onChange={(e) => setSelectedLocation(e.target.value)}
                style={{ border: 'none', background: 'none', fontWeight: 'bold', color: '#003c71' }}
              >
                {locations.map((loc) => (
                  <option key={loc} value={loc}>{loc}</option>
                ))}
              </select>
            </div>

            <button
              className="mobile-nav-item drawer-account-link"
              style={{ display: 'flex', alignItems: 'center', gap: '8px', background: 'none', border: 'none', width: '100%', textAlign: 'left', padding: '8px 0', cursor: 'pointer', color: 'inherit', font: 'inherit' }}
              onClick={() => {
                setIsMobileMenuOpen(false);
                setIsLoginOpen(true);
                setAuthMode('login');
              }}
            >
              <User size={18} style={{ color: '#00a3ad' }} />
              <span>User Account</span>
            </button>

            <a href="#/cart" className={`mobile-nav-item drawer-cart-link ${isCartAnimating ? 'cart-animate-trigger' : ''}`} style={{ display: 'flex', alignItems: 'center', gap: '8px' }} onClick={() => setIsMobileMenuOpen(false)}>
              <ShoppingCart size={18} style={{ color: '#00a3ad' }} />
              <span>Shopping Cart ({cartCount})</span>
            </a>
          </div>

          <div style={{ borderTop: '1px solid var(--line)', paddingTop: '16px', marginTop: '8px', display: 'flex', flexDirection: 'column', gap: '12px' }}>
            <span style={{ fontWeight: '800', color: 'var(--muted)', textTransform: 'uppercase', letterSpacing: '0.5px' }}>Contact Support</span>
            <a href="https://wa.me/91 8939905115" target="_blank" rel="noopener noreferrer" style={{ display: 'flex', alignItems: 'center', gap: '8px', color: '#25D366', fontWeight: 'bold' }}>
              <FaWhatsapp size={20} />
              <span>Chat on WhatsApp</span>
            </a>
            <a href="tel:+91 8939905115" style={{ display: 'flex', alignItems: 'center', gap: '8px', color: 'var(--blue)', fontWeight: 'bold', fontSize: '0.95rem' }}>
              <Phone size={18} />
              <span>Call +91  8939905115</span>
            </a>
          </div>
        </div>
      </div>

      <DownloadReportModal isOpen={isReportModalOpen} onClose={() => setIsReportModalOpen(false)} />

      {/* Login / Sign Up Modal */}
      {isLoginOpen && (
        <div className="login-modal-backdrop" onClick={() => { setIsLoginOpen(false); setAuthMode('login'); }}>
          <div className="login-modal-card" onClick={(e) => e.stopPropagation()}>
            <div className="login-modal-header">
              <h3 className="login-modal-title">{authMode === 'register' ? 'Register' : 'Login/Sign Up'}</h3>
              <button className="login-modal-close-btn" onClick={() => { setIsLoginOpen(false); setAuthMode('login'); }}>
                <X size={20} />
              </button>
            </div>

            {authMode === 'login' ? (
              <>
                <p className="login-modal-subtitle">Enter your mobile number and password to login</p>

                <div className="login-input-group">
                  <div className="login-country-code">
                    <span>+91</span>
                    <ChevronDown size={14} />
                  </div>
                  <input
                    type="tel"
                    placeholder="Enter Mobile Number"
                    className="login-mobile-input"
                    maxLength="10"
                    value={loginMobile}
                    onChange={(e) => setLoginMobile(e.target.value)}
                  />
                </div>

                <div style={{ marginBottom: '16px' }}>
                  <input
                    type="password"
                    placeholder="Password"
                    style={authInputStyle}
                    value={loginPassword}
                    onChange={(e) => setLoginPassword(e.target.value)}
                    autoComplete="current-password"
                  />
                </div>

                <label className="login-whatsapp-consent">
                  <input type="checkbox" defaultChecked />
                  <span className="consent-checkbox-custom"></span>
                  <span className="consent-text">Share health tips, appointment details and offers with me on Whatsapp</span>
                </label>

                <p className="login-terms-text">
                  By clicking Continue, you agree to Super Lab's <a href="#/privacy">Privacy Policy</a> & <a href="#/terms">Terms and Conditions</a>
                </p>
                <button className="btn-login-submit" onClick={async () => {
                  if (!loginMobile || !loginPassword) {
                    alert('Please enter your mobile number and password.');
                    return;
                  }
                  const res = await loginCustomer({ mobile: loginMobile, password: loginPassword });
                  if (res.success) {
                    if (res.data) setSuperlabCustomer(res.data);
                    alert(res.message || 'Logged in successfully!');
                    resetAuthForms();
                    setIsLoginOpen(false);
                    window.location.hash = '#/profile';
                  } else {
                    alert(res.message || 'Login failed.');
                  }
                }}>
                  Login
                </button>

                <p style={{ textAlign: 'center', marginTop: '20px', fontSize: '0.9rem', color: 'var(--muted)', fontWeight: '600' }}>
                  Don't have an account?{' '}
                  <span
                    style={{ color: 'var(--teal)', fontWeight: '700', cursor: 'pointer', borderBottom: '1px dashed var(--teal)' }}
                    onClick={() => setAuthMode('register')}
                  >
                    Register
                  </span>
                </p>
              </>
            ) : (
              <>
                <p className="login-modal-subtitle">Join us for a better health journey</p>

                <div style={{ display: 'flex', flexDirection: 'column', gap: '12px', marginBottom: '20px' }}>
                  <div>
                    <input
                      type="text"
                      placeholder="Full Name"
                      style={{ border: '1.5px solid #cbd5e1', borderRadius: '8px', padding: '10px 14px', width: '100%', boxSizing: 'border-box', fontWeight: '600', fontSize: '0.95rem' }}
                      value={registerName}
                      onChange={(e) => setRegisterName(e.target.value)}
                    />
                  </div>

                  <div>
                    <input
                      type="email"
                      placeholder="Email Address"
                      style={{ border: '1.5px solid #cbd5e1', borderRadius: '8px', padding: '10px 14px', width: '100%', boxSizing: 'border-box', fontWeight: '600', fontSize: '0.95rem' }}
                      value={registerEmail}
                      onChange={(e) => setRegisterEmail(e.target.value)}
                    />
                  </div>

                  <div className="login-input-group" style={{ marginBottom: 0 }}>
                    <div className="login-country-code" style={{ padding: '10px 14px' }}>
                      <span>+91</span>
                      <ChevronDown size={14} />
                    </div>
                    <input
                      type="tel"
                      placeholder="Mobile Number"
                      className="login-mobile-input"
                      maxLength="10"
                      style={{ padding: '10px 14px' }}
                      value={registerMobile}
                      onChange={(e) => setRegisterMobile(e.target.value)}
                    />
                  </div>

                  <div>
                    <input
                      type="password"
                      placeholder="Password (min. 8 characters)"
                      style={authInputStyle}
                      value={registerPassword}
                      onChange={(e) => setRegisterPassword(e.target.value)}
                      autoComplete="new-password"
                    />
                  </div>

                  <div>
                    <input
                      type="password"
                      placeholder="Confirm Password"
                      style={authInputStyle}
                      value={registerPasswordConfirm}
                      onChange={(e) => setRegisterPasswordConfirm(e.target.value)}
                      autoComplete="new-password"
                    />
                  </div>

                  <div style={{ display: 'grid', gridTemplateColumns: '1fr 1.2fr 1fr', gap: '8px' }}>
                    <input
                      type="number"
                      placeholder="Age"
                      style={{ border: '1.5px solid #cbd5e1', borderRadius: '8px', padding: '10px', width: '100%', boxSizing: 'border-box', fontWeight: '600', fontSize: '0.9rem' }}
                      value={registerAge}
                      onChange={(e) => setRegisterAge(e.target.value)}
                    />
                    <select
                      style={{ border: '1.5px solid #cbd5e1', borderRadius: '8px', padding: '10px', width: '100%', boxSizing: 'border-box', backgroundColor: '#fff', fontWeight: '600', fontSize: '0.9rem', color: '#334155' }}
                      value={registerGender}
                      onChange={(e) => setRegisterGender(e.target.value)}
                    >
                      <option value="Male">Male</option>
                      <option value="Female">Female</option>
                      <option value="Other">Other</option>
                    </select>
                    <select
                      style={{ border: '1.5px solid #cbd5e1', borderRadius: '8px', padding: '10px', width: '100%', boxSizing: 'border-box', backgroundColor: '#fff', fontWeight: '600', fontSize: '0.9rem', color: '#334155' }}
                      value={registerBloodGroup}
                      onChange={(e) => setRegisterBloodGroup(e.target.value)}
                    >
                      <option value="Unknown">Blood</option>
                      <option value="A+">A+</option>
                      <option value="A-">A-</option>
                      <option value="B+">B+</option>
                      <option value="B-">B-</option>
                      <option value="AB+">AB+</option>
                      <option value="AB-">AB-</option>
                      <option value="O+">O+</option>
                      <option value="O-">O-</option>
                    </select>
                  </div>
                </div>

                <label className="login-whatsapp-consent" style={{ marginBottom: '16px' }}>
                  <input type="checkbox" defaultChecked />
                  <span className="consent-checkbox-custom"></span>
                  <span className="consent-text" style={{ fontSize: '0.78rem' }}>Share health tips, reports and offers on Whatsapp</span>
                </label>

                <button className="btn-login-submit" onClick={async () => {
                  if (!registerName || !registerMobile || !registerPassword) {
                    alert('Please fill in your name, mobile number, and password.');
                    return;
                  }
                  if (registerPassword.length < 8) {
                    alert('Password must be at least 8 characters.');
                    return;
                  }
                  if (registerPassword !== registerPasswordConfirm) {
                    alert('Passwords do not match.');
                    return;
                  }
                  const payload = {
                    name: registerName,
                    email: registerEmail || null,
                    mobile: registerMobile,
                    password: registerPassword,
                    password_confirmation: registerPasswordConfirm,
                    age: registerAge ? parseInt(registerAge) : null,
                    gender: registerGender,
                    blood_group: registerBloodGroup,
                  };
                  const res = await registerCustomer(payload);
                  if (res.success) {
                    if (res.data) setSuperlabCustomer(res.data);
                    alert(res.message || 'Registration Successful!');
                    resetAuthForms();
                    setIsLoginOpen(false);
                    window.location.hash = '#/profile';
                  } else {
                    alert(res.message || 'Registration failed.');
                  }
                }}>
                  Register
                </button>

                <p style={{ textAlign: 'center', marginTop: '16px', fontSize: '0.9rem', color: 'var(--muted)', fontWeight: '600' }}>
                  Already have an account?{' '}
                  <span
                    style={{ color: 'var(--teal)', fontWeight: '700', cursor: 'pointer', borderBottom: '1px dashed var(--teal)' }}
                    onClick={() => setAuthMode('login')}
                  >
                    Login
                  </span>
                </p>
              </>
            )}
          </div>
        </div>
      )}

      {/* ISO Certificate Modal */}
      {isIsoModalOpen && (
        <div
          onClick={() => setIsIsoModalOpen(false)}
          style={{
            position: 'fixed',
            top: 0,
            left: 0,
            right: 0,
            bottom: 0,
            backgroundColor: 'rgba(15, 23, 42, 0.6)',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            zIndex: 9999,
            backdropFilter: 'blur(4px)',
            animation: 'fadeInISO 0.2s ease-out'
          }}
        >
          <style>{`
            @keyframes fadeInISO {
              from { opacity: 0; }
              to { opacity: 1; }
            }
            @keyframes slideUpISO {
              from { transform: scale(0.95) translateY(20px); opacity: 0; }
              to { transform: scale(1) translateY(0); opacity: 1; }
            }
            @media (max-width: 480px) {
              .iso-modal-content {
                padding: 16px !important;
                border-radius: 12px !important;
              }
              .iso-modal-title {
                font-size: 1.1rem !important;
                margin-bottom: 12px !important;
              }
              .iso-modal-close-btn {
                top: 12px !important;
                right: 12px !important;
                width: 28px !important;
                height: 28px !important;
              }
            }
          `}</style>
          <div
            onClick={(e) => e.stopPropagation()}
            className="iso-modal-content"
            style={{
              position: 'relative',
              backgroundColor: '#ffffff',
              borderRadius: '16px',
              padding: '24px',
              maxWidth: '600px',
              width: '90%',
              boxShadow: '0 25px 50px -12px rgba(0, 0, 0, 0.25)',
              display: 'flex',
              flexDirection: 'column',
              alignItems: 'center',
              animation: 'slideUpISO 0.3s cubic-bezier(0.34, 1.56, 0.64, 1)'
            }}
          >
            <button
              onClick={() => setIsIsoModalOpen(false)}
              className="iso-modal-close-btn"
              style={{
                position: 'absolute',
                top: '16px',
                right: '16px',
                background: '#f1f5f9',
                border: 'none',
                borderRadius: '50%',
                width: '32px',
                height: '32px',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                cursor: 'pointer',
                color: '#64748b',
                transition: 'background-color 0.2s'
              }}
              onMouseEnter={(e) => e.currentTarget.style.backgroundColor = '#e2e8f0'}
              onMouseLeave={(e) => e.currentTarget.style.backgroundColor = '#f1f5f9'}
            >
              <X size={18} />
            </button>

            <h3 className="iso-modal-title" style={{ fontSize: '1.25rem', fontWeight: '800', color: 'var(--blue)', marginTop: 0, marginBottom: '4px', textAlign: 'center' }}>
              ISO 15189:2022 Certificate of Compliance
            </h3>
            <p style={{ fontSize: '0.85rem', color: '#64748b', marginTop: 0, marginBottom: '16px', textAlign: 'center', fontWeight: '500' }}>
              Phlebee Healthcare Network Private Limited • Cert No: 101020626
            </p>

            <div style={{ width: '100%', display: 'flex', justifyContent: 'center', overflow: 'hidden', borderRadius: '12px', border: '1px solid var(--line)', backgroundColor: '#f8fafc', padding: '8px' }}>
              <img
                src="/iso-certificate.png?v=20260909"
                alt="ISO 15189:2022 Certificate of Compliance - Phlebee Healthcare Network"
                style={{
                  width: '100%',
                  maxHeight: '65vh',
                  objectFit: 'contain',
                  borderRadius: '6px'
                }}
              />
            </div>

            <div style={{ display: 'flex', gap: '12px', marginTop: '16px', width: '100%', justifyContent: 'center', flexWrap: 'wrap' }}>
              <a
                href="/iso-certificate.pdf?v=20260909"
                target="_blank"
                rel="noopener noreferrer"
                style={{
                  display: 'inline-flex',
                  alignItems: 'center',
                  gap: '8px',
                  backgroundColor: 'var(--blue)',
                  color: '#ffffff',
                  padding: '8px 16px',
                  borderRadius: '8px',
                  fontSize: '0.85rem',
                  fontWeight: '600',
                  textDecoration: 'none',
                  transition: 'background-color 0.2s'
                }}
              >
                <Download size={16} /> Download PDF Certificate
              </a>
              <a
                href="https://www.qvcert.co.uk"
                target="_blank"
                rel="noopener noreferrer"
                style={{
                  display: 'inline-flex',
                  alignItems: 'center',
                  gap: '8px',
                  backgroundColor: '#f1f5f9',
                  color: '#334155',
                  padding: '8px 16px',
                  borderRadius: '8px',
                  fontSize: '0.85rem',
                  fontWeight: '600',
                  textDecoration: 'none',
                  border: '1px solid #cbd5e1',
                  transition: 'background-color 0.2s'
                }}
              >
                <ExternalLink size={16} /> Verify on QVCert.co.uk
              </a>
            </div>
          </div>
        </div>
      )}
    </header>
  );
};

export default Header;
