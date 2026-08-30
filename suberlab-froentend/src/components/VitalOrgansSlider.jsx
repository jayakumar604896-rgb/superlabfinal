import { useState, useEffect } from 'react';
import { ChevronLeft, ChevronRight } from 'lucide-react';
import { navigateToLabTestsFilter } from '../utils/labTestsNavigation';

const VitalOrgansSlider = () => {
  const baseOrgans = [
    { name: 'Kidney Test', img: '/vital organs/kidney test.png' },
    { name: 'Cancer Screening Test', img: '/vital organs/cancer screening test.png' },
    { name: 'Prostate Test', img: '/vital organs/prostate test.png' },
    { name: 'Thyroid Test', img: '/vital organs/thyroid test.png' },
    { name: 'Liver Test', img: '/vital organs/liver test.png' },
    { name: 'Heart Test', img: '/vital organs/heart test.png' },
    { name: 'Lung Test', img: '/vital organs/lung test.png' },
    { name: 'Gastrointestinal Test', img: '/vital organs/gastrointestinal test.png' }
  ];

  // Prepend last 6 and append first 6 for a seamless infinite loop
  const itemsVisible = 6;
  const organs = [
    ...baseOrgans.slice(-itemsVisible),
    ...baseOrgans,
    ...baseOrgans.slice(0, itemsVisible)
  ];

  const [startIndex, setStartIndex] = useState(itemsVisible); // Start at the real first element
  const [isTransitioning, setIsTransitioning] = useState(true);

  const handleNext = () => {
    if (!isTransitioning) return;
    setStartIndex((prev) => prev + 1);
  };

  const handlePrev = () => {
    if (!isTransitioning) return;
    setStartIndex((prev) => prev - 1);
  };

  const handleTransitionEnd = () => {
    if (startIndex >= baseOrgans.length + itemsVisible) {
      setIsTransitioning(false);
      setStartIndex(itemsVisible);
    }
    else if (startIndex <= 0) {
      setIsTransitioning(false);
      setStartIndex(baseOrgans.length);
    }
  };

  useEffect(() => {
    if (!isTransitioning) {
      const timer = setTimeout(() => {
        setIsTransitioning(true);
      }, 50);
      return () => clearTimeout(timer);
    }
  }, [isTransitioning]);

  return (
    <section className="vital-organs-section">
      <div className="vital-organs-container">
        
        {/* Header Row */}
        <div className="section-header-row">
          <h2 className="section-main-title">Vital Organs</h2>
          <button className="btn-view-all-packages" onClick={() => navigateToLabTestsFilter('Vital Organs')}>
            View All
          </button>
        </div>

        {/* Viewport and Controls */}
        <div className="featured-checkups-grid-wrapper">
          {/* Left Arrow */}
          <button className="grid-arrow-left-floating" onClick={handlePrev}>
            <ChevronLeft size={20} />
          </button>

          {/* Sliding Grid */}
          <div className="featured-checkups-grid">
            <div 
              className="featured-checkups-track"
              onTransitionEnd={handleTransitionEnd}
              style={{
                display: 'flex',
                gap: '24px',
                transform: `translateX(-${startIndex * (150 + 24)}px)`,
                transition: isTransitioning ? 'transform 0.5s cubic-bezier(0.16, 1, 0.3, 1)' : 'none'
              }}
            >
              {organs.map((organ, index) => (
                <button
                  key={`${organ.name}-${index}`}
                  type="button"
                  className="vital-organ-card"
                  onClick={() => navigateToLabTestsFilter(organ.name)}
                  style={{ background: 'none', border: 'none', cursor: 'pointer', padding: 0 }}
                >
                  <div className="vital-organ-circle">
                    <img 
                      src={organ.img} 
                      alt={organ.name} 
                      className="vital-organ-img"
                      onError={(e) => {
                        e.target.style.display = 'none';
                      }}
                    />
                  </div>
                  <span className="vital-organ-name">{organ.name}</span>
                </button>
              ))}
            </div>
          </div>

          {/* Right Arrow */}
          <button className="grid-arrow-right-floating" onClick={handleNext}>
            <ChevronRight size={20} />
          </button>
        </div>

      </div>
    </section>
  );
};

export default VitalOrgansSlider;
