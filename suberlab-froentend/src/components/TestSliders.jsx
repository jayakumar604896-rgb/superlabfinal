import { useState, useEffect, useRef } from 'react';
import { ChevronLeft, ChevronRight } from 'lucide-react';
import { fetchServices } from '../services/api';
import { getOriginalPrice, getSalePrice } from '../utils/pricing';
import { navigateToLabTestsFilter } from '../utils/labTestsNavigation';

const CircularTestSlider = ({ title, category, items }) => {
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
      
      const cardWidthAndGap = 280 + 20;
      const index = Math.min(
        Math.round(scrollLeft / cardWidthAndGap),
        items.length - 1
      );
      setActiveDot(index);
    }
  };

  useEffect(() => {
    const el = scrollRef.current;
    if (el) {
      el.addEventListener('scroll', checkScroll);
      checkScroll();
      const timer = setTimeout(checkScroll, 300);
      window.addEventListener('resize', checkScroll);
      return () => {
        el.removeEventListener('scroll', checkScroll);
        window.removeEventListener('resize', checkScroll);
        clearTimeout(timer);
      };
    }
  }, [items]);

  const handleNext = () => {
    if (scrollRef.current) {
      // scroll by roughly 1 item card: 280 + 20 = 300
      scrollRef.current.scrollBy({ left: 300, behavior: 'smooth' });
    }
  };

  const handlePrev = () => {
    if (scrollRef.current) {
      scrollRef.current.scrollBy({ left: -300, behavior: 'smooth' });
    }
  };

  const handleViewAll = () => {
    navigateToLabTestsFilter(category);
  };

  return (
    <div className="test-slider-category-wrapper" style={{ marginBottom: '32px' }}>
      {/* Header Row */}
      <div className="section-header-row">
        <h2 className="section-main-title">{title}</h2>
        <button className="btn-view-all-packages" onClick={handleViewAll}>
          View All
        </button>
      </div>

      {/* Slider viewport */}
      <div className="featured-checkups-grid-wrapper">
        {/* Left Arrow */}
        <button 
          className="grid-arrow-left-floating" 
          onClick={handlePrev}
          disabled={!canScrollLeft}
          style={{ opacity: !canScrollLeft ? 0.3 : 1, cursor: !canScrollLeft ? 'not-allowed' : 'pointer' }}
        >
          <ChevronLeft size={20} />
        </button>

        {/* Sliding Grid */}
        <div className="featured-checkups-grid" ref={scrollRef} style={{ overflowX: 'auto', scrollbarWidth: 'none', msOverflowStyle: 'none' }}>
          <div
            className="featured-checkups-track"
            style={{
              display: 'flex',
              gap: '20px'
            }}
          >
            {items.map((item, index) => (
              <div key={index} className="test-item-card premium-tilt-card">
                <h3 className="test-card-title">{item.name}</h3>
                
                <div className="test-card-bottom">
                  <span className="test-card-price">₹ {item.price}</span>
                  <div className="test-card-actions">
                    <a 
                      href={item.hash || (item.slug ? `#/test/${item.slug}` : '#/lab-tests')} 
                      className="test-link-know-more"
                    >
                      Know More
                    </a>
                    {(() => {
                      const itemId = item.id || item.name.toLowerCase().replace(/\s+/g, '-');
                      const isAdded = cartItems.some(i => 
                        (i.id != null && itemId != null && String(i.id).trim() === String(itemId).trim()) || 
                        (i.name && item.name && i.name.toLowerCase().trim() === item.name.toLowerCase().trim())
                      );
                      const itemPayload = { 
                        id: itemId, 
                        name: item.name, 
                        category: item.category || 'Diagnostic Test', 
                        price: getSalePrice(item) || 150,
                        type: 'test',
                      };
                      return (
                        <button
                          className={`btn-add-test ${isAdded ? 'added' : ''}`}
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
              </div>
            ))}
          </div>
        </div>

        {/* Right Arrow */}
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
      <div className="slider-dots-container" style={{ display: 'flex', justifyContent: 'center', gap: '8px', marginTop: '16px' }}>
        {items.map((_, index) => (
          <button
            key={index}
            onClick={() => {
              if (scrollRef.current) {
                scrollRef.current.scrollTo({ left: index * (280 + 20), behavior: 'smooth' });
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
  );
};

const TestSliders = () => {
  const [allServices, setAllServices] = useState([]);

  useEffect(() => {
    fetchServices().then(({ data }) => {
      if (data && data.length > 0) {
        setAllServices(data);
      }
    });
  }, []);

  const getCategoryTests = (categoryName) => {
    return allServices.filter(s => {
      const cat = typeof s.category === 'object' ? s.category?.name : s.category;
      return cat && cat.toLowerCase().trim() === categoryName.toLowerCase().trim();
    }).map(s => ({
      id: s.id,
      name: s.name || s.title,
      slug: s.slug,
      hash: s.hash || (s.slug ? `#/test/${s.slug}` : null),
      price: getSalePrice(s) || 150,
      originalPrice: getOriginalPrice(s),
      category: categoryName
    }));
  };

  const pregnancyTests = getCategoryTests('Pregnancy');
  const heartTests = getCategoryTests('Heart');
  const hivTests = getCategoryTests('HIV');

  return (
    <section className="test-sliders-section">
      <div className="test-sliders-container">
        {pregnancyTests.length > 0 && <CircularTestSlider title="Pregnancy Tests" category="Pregnancy" items={pregnancyTests} />}
        {heartTests.length > 0 && <CircularTestSlider title="Heart Tests" category="Heart" items={heartTests} />}
        {hivTests.length > 0 && <CircularTestSlider title="HIV Tests" category="HIV" items={hivTests} />}
      </div>
    </section>
  );
};

export default TestSliders;
