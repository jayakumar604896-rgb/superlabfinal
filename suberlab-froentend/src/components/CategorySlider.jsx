import { ChevronLeft, ChevronRight } from 'lucide-react';
import { useState, useEffect, useRef } from 'react';
import { fetchCategories } from '../services/api';

const CategorySlider = () => {
  const [categories, setCategories] = useState([]);

  useEffect(() => {
    fetchCategories().then(({ data }) => {
      if (data && data.length > 0) {
        const apiCats = data.map(c => ({
          id: c.id,
          name: (c.category_name || c.name).toUpperCase(),
          slug: c.slug,
          img: c.image || `/tests/${c.slug}.png`
        }));
        setCategories(apiCats);
      }
    });
  }, []);
  const scrollRef = useRef(null);
  const [canScrollLeft, setCanScrollLeft] = useState(false);
  const [canScrollRight, setCanScrollRight] = useState(true);
  const [activeDot, setActiveDot] = useState(0);

  const checkScroll = () => {
    if (scrollRef.current) {
      const { scrollLeft, scrollWidth, clientWidth } = scrollRef.current;
      setCanScrollLeft(scrollLeft > 5);
      setCanScrollRight(scrollLeft + clientWidth < scrollWidth - 5);
      
      const cardWidthAndGap = 180 + 16;
      const index = Math.min(
        Math.round(scrollLeft / cardWidthAndGap),
        categories.length - 1
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
  }, []);

  const handleNext = () => {
    if (scrollRef.current) {
      // scroll by roughly 2 items: (180 + 16) * 2 = 392
      scrollRef.current.scrollBy({ left: 392, behavior: 'smooth' });
    }
  };

  const handlePrev = () => {
    if (scrollRef.current) {
      scrollRef.current.scrollBy({ left: -392, behavior: 'smooth' });
    }
  };

  return (
    <section className="category-slider-section">
      <div className="category-slider-container">
        
        {/* Header Row */}
        <div className="section-header-row">
          <h2 className="section-main-title">Popular Health-Checkup Categories</h2>
          <button className="btn-view-all-packages" onClick={() => window.location.hash = '#/lab-tests'}>
            View All
          </button>
        </div>

        {/* Viewport & Controls */}
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

          {/* Track container */}
          <div className="featured-checkups-grid" ref={scrollRef} style={{ overflowX: 'auto', scrollbarWidth: 'none', msOverflowStyle: 'none' }}>
            <div 
              className="featured-checkups-track"
              style={{
                display: 'flex',
                gap: '16px'
              }}
            >
              {categories.map((cat, index) => (
                <div key={index} className="category-card-item">
                  <div className="category-card-icon">
                    <img 
                      src={cat.img} 
                      alt={cat.name} 
                      className="category-img-icon"
                      onError={(e) => {
                        e.target.style.display = 'none';
                      }}
                    />
                  </div>
                  <span className="category-card-name">{cat.name}</span>
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
        <div className="slider-dots-container" style={{ display: 'flex', justifyContent: 'center', gap: '8px', marginTop: '20px', flexWrap: 'wrap', padding: '0 20px' }}>
          {categories.map((_, index) => {
            // Since there are 50 categories, rendering 50 dots might be too many.
            // Let's render a dot for every 2 items to keep it clean and readable.
            if (index % 2 !== 0) return null;
            const dotIndex = Math.floor(index / 2);
            const activeDotIndex = Math.floor(activeDot / 2);
            return (
              <button
                key={index}
                onClick={() => {
                  if (scrollRef.current) {
                    scrollRef.current.scrollTo({ left: index * (180 + 16), behavior: 'smooth' });
                  }
                }}
                style={{
                  width: activeDotIndex === dotIndex ? '12px' : '8px',
                  height: '8px',
                  borderRadius: '4px',
                  border: 'none',
                  backgroundColor: activeDotIndex === dotIndex ? 'var(--orange)' : 'var(--line)',
                  cursor: 'pointer',
                  padding: 0,
                  transition: 'all 0.2s',
                  marginBottom: '6px'
                }}
                title={`Go to category ${index + 1}`}
              />
            );
          })}
        </div>

      </div>
    </section>
  );
};

export default CategorySlider;
