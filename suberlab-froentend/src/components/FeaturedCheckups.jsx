import { useState, useEffect, useRef } from 'react';
import { ChevronRight, ChevronLeft } from 'lucide-react';
import { fetchPackages } from '../services/api';
import { navigateToLabTestsFilter } from '../utils/labTestsNavigation';

const FeaturedCheckups = () => {
  const [baseCheckups, setBaseCheckups] = useState([]);

  useEffect(() => {
    fetchPackages().then(({ data }) => {
      if (data && data.length > 0) {
        const apiPkgs = data.map(p => ({
          id: p.id,
          name: p.name,
          slug: p.slug,
          testCount: p.tests_included_count || p.testsCount || 90,
          originalPrice: p.original_price,
          discountedPrice: p.offer_price,
          discount: p.discount ? `${p.discount}% OFF` : '40% OFF',
          badge: p.badge || 'POPULAR',
          link: p.hash || (p.slug ? `#/package/${p.slug}` : '#/lab-tests')
        }));
        setBaseCheckups(apiPkgs);
      }
    });
  }, []);

  const scrollRef = useRef(null);
  const [canScrollLeft, setCanScrollLeft] = useState(false);
  const [canScrollRight, setCanScrollRight] = useState(true);
  const [activeDot, setActiveDot] = useState(0);
  const [cartItems, setCartItems] = useState(() => {
    return window.getSuperlabCart ? window.getSuperlabCart() : [];
  });

  useEffect(() => {
    const handleCartUpdate = () => {
      if (window.getSuperlabCart) {
        setCartItems(window.getSuperlabCart());
      }
    };
    window.addEventListener('superlab_cart_update', handleCartUpdate);
    return () => window.removeEventListener('superlab_cart_update', handleCartUpdate);
  }, []);

  const checkScroll = () => {
    if (scrollRef.current) {
      const { scrollLeft, scrollWidth, clientWidth } = scrollRef.current;
      setCanScrollLeft(scrollLeft > 5);
      setCanScrollRight(scrollLeft + clientWidth < scrollWidth - 5);
      
      const cardWidthAndGap = 300 + 20;
      const index = Math.min(
        Math.round(scrollLeft / cardWidthAndGap),
        baseCheckups.length - 1
      );
      setActiveDot(index);
    }
  };

  useEffect(() => {
    const el = scrollRef.current;
    if (el) {
      el.addEventListener('scroll', checkScroll);
      checkScroll();
      // Wait for layout/images load
      const timer = setTimeout(checkScroll, 300);
      window.addEventListener('resize', checkScroll);
      return () => {
        el.removeEventListener('scroll', checkScroll);
        window.removeEventListener('resize', checkScroll);
        clearTimeout(timer);
      };
    }
  }, []);

  const handleNext = () => {
    if (scrollRef.current) {
      scrollRef.current.scrollBy({ left: 320, behavior: 'smooth' });
    }
  };

  const handlePrev = () => {
    if (scrollRef.current) {
      scrollRef.current.scrollBy({ left: -320, behavior: 'smooth' });
    }
  };

  const handleViewAll = () => {
    navigateToLabTestsFilter('Full Body Health');
  };

  return (
    <section className="featured-checkups-section">
      <div className="featured-checkups-container">
        {/* Header Row */}
        <div className="section-header-row">
          <h2 className="section-main-title">Full Body Health Checks</h2>
          <button className="btn-view-all-packages" onClick={handleViewAll}>
            View All
          </button>
        </div>

        {/* Cards Row Grid Wrapper */}
        <div className="featured-checkups-grid-wrapper">
          {/* Left Floating Arrow */}
          <button 
            className="grid-arrow-left-floating" 
            onClick={handlePrev}
            disabled={!canScrollLeft}
            style={{ opacity: !canScrollLeft ? 0.3 : 1, cursor: !canScrollLeft ? 'not-allowed' : 'pointer' }}
          >
            <ChevronLeft size={20} />
          </button>

          {/* Sliding Grid Viewport */}
          <div className="featured-checkups-grid" ref={scrollRef} style={{ overflowX: 'auto', scrollbarWidth: 'none', msOverflowStyle: 'none' }}>
            <div 
              className="featured-checkups-track"
              style={{
                display: 'flex',
                gap: '20px'
              }}
            >
              {baseCheckups.map((check, index) => (
                <div key={index} className="checkup-card premium-tilt-card">
                  {/* Badges top bar */}
                  <div className="checkup-card-badges">
                    {check.badge ? (
                      <span className="badge-most-booked">{check.badge}</span>
                    ) : (
                      <span />
                    )}
                    {check.discount && (
                      <span className="badge-discount">{check.discount}</span>
                    )}
                  </div>

                  {/* Package Name */}
                  <h3 className="checkup-card-title">{check.name}</h3>

                  {/* Test Count */}
                  <p className="checkup-card-tests">Includes {check.testCount} tests</p>

                  {/* Pricing row */}
                  <div className="checkup-card-pricing">
                    {check.originalPrice && check.originalPrice > check.discountedPrice && (
                      <span className="price-original">₹ {check.originalPrice}</span>
                    )}
                    <span className="price-discounted">₹ {check.discountedPrice}</span>
                  </div>

                  {/* Actions row */}
                  <div className="checkup-card-actions">
                    <a href={check.link} className="link-know-more">
                      Know More
                    </a>
                    {(() => {
                      const checkId = check.id || check.name.toLowerCase().replace(/\s+/g, '-');
                      const isAdded = cartItems.some(item => 
                        (item.id != null && checkId != null && String(item.id).trim() === String(checkId).trim()) || 
                        (item.name && check.name && item.name.toLowerCase().trim() === check.name.toLowerCase().trim())
                      );
                      const itemPayload = { 
                        id: checkId, 
                        name: check.name, 
                        category: 'Health Checkup', 
                        type: 'package',
                        price: check.discountedPrice || 999, 
                        ...(check.originalPrice ? { originalPrice: check.originalPrice } : {}),
                      };
                      return (
                        <button 
                          className={`btn-book-checkup ${isAdded ? 'added' : ''}`}
                          onClick={() => {
                            if (isAdded) {
                              window.removeFromSuperlabCart(itemPayload);
                            } else {
                              window.addToSuperlabCart(itemPayload);
                            }
                          }}
                        >
                          {isAdded ? 'ADDED' : 'ADD'}
                        </button>
                      );
                    })()}
                  </div>
                </div>
              ))}
            </div>
          </div>

          {/* Right Floating Arrow */}
          <button 
            className="grid-arrow-right-floating" 
            onClick={handleNext}
            disabled={!canScrollRight}
            style={{ opacity: !canScrollRight ? 0.3 : 1, cursor: !canScrollRight ? 'not-allowed' : 'pointer' }}
          >
            <ChevronRight size={20} />
          </button>
        </div>

        {/* Dots Indicator */}
        <div className="slider-dots-container" style={{ display: 'flex', justifyContent: 'center', gap: '8px', marginTop: '20px' }}>
          {baseCheckups.map((_, index) => (
            <button
              key={index}
              onClick={() => {
                if (scrollRef.current) {
                  scrollRef.current.scrollTo({ left: index * (300 + 20), behavior: 'smooth' });
                }
              }}
              style={{
                width: activeDot === index ? '12px' : '8px',
                height: '8px',
                borderRadius: '4px',
                border: 'none',
                backgroundColor: activeDot === index ? 'var(--orange)' : 'var(--line)',
                cursor: 'pointer',
                padding: 0,
                transition: 'all 0.2s'
              }}
              title={`Go to slide ${index + 1}`}
            />
          ))}
        </div>
      </div>
    </section>
  );
};

export default FeaturedCheckups;
