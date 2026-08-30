import { useState, useEffect } from 'react';
import { ChevronLeft, ChevronRight, Star } from 'lucide-react';
import { fetchTestimonials } from '../services/api';

const HomepageReviews = () => {
  const [reviews, setReviews] = useState([]);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    fetchTestimonials().then(({ data }) => {
      if (data && data.length > 0) {
        setReviews(data.map(r => ({
          id: r.id,
          name: r.name,
          rating: r.rating || 5,
          text: r.comment || r.text || r.content,
        })));
      }
      setIsLoading(false);
    });
  }, []);

  const [currentIndex, setCurrentIndex] = useState(0);
  const [transitionEnabled, setTransitionEnabled] = useState(true);

  const nextReview = () => {
    if (!transitionEnabled) return;
    setCurrentIndex((prev) => prev + 1);
  };

  const prevReview = () => {
    if (!transitionEnabled) return;
    if (currentIndex === 0) {
      setTransitionEnabled(false);
      setCurrentIndex(reviews.length);
      setTimeout(() => {
        setTransitionEnabled(true);
        setCurrentIndex(reviews.length - 1);
      }, 20);
    } else {
      setCurrentIndex((prev) => prev - 1);
    }
  };

  useEffect(() => {
    if (currentIndex === reviews.length) {
      const timer = setTimeout(() => {
        setTransitionEnabled(false);
        setCurrentIndex(0);
      }, 500);
      return () => clearTimeout(timer);
    }
  }, [currentIndex, reviews.length]);

  useEffect(() => {
    if (!transitionEnabled) {
      const timer = setTimeout(() => {
        setTransitionEnabled(true);
      }, 20);
      return () => clearTimeout(timer);
    }
  }, [transitionEnabled]);

  useEffect(() => {
    const timer = setInterval(() => {
      nextReview();
    }, 2000);
    return () => clearInterval(timer);
  }, [transitionEnabled]);

  if (isLoading || reviews.length === 0) {
    return null;
  }

  const extendedReviews = [...reviews, ...reviews.slice(0, Math.min(3, reviews.length))];

  const renderStars = (rating) => {
    return (
      <div style={{ display: 'flex', gap: '2px' }}>
        {[1, 2, 3, 4, 5].map((s) => (
          <Star
            key={s}
            size={15}
            fill={s <= rating ? "#ff9f1c" : "none"}
            stroke={s <= rating ? "#ff9f1c" : "#cbd5e1"}
          />
        ))}
      </div>
    );
  };

  return (
    <div className="page-section-container text-left" style={{ marginTop: '50px', marginBottom: '80px' }}>
      <div style={{
        backgroundColor: '#ebf4fc',
        color: 'var(--blue)',
        fontWeight: '800',
        fontSize: '1.25rem',
        padding: '14px 24px',
        borderRadius: '12px',
        marginBottom: '24px',
        fontFamily: 'var(--sans)'
      }}>
        Customer Reviews
      </div>

      <div style={{ display: 'flex', gap: '24px', flexWrap: 'wrap', alignItems: 'stretch' }}>
        <div style={{
          flex: '0 0 290px',
          backgroundColor: '#ffffff',
          border: '1px solid var(--line)',
          borderRadius: '16px',
          padding: '24px',
          boxShadow: 'var(--shadow-sm)',
          display: 'flex',
          flexDirection: 'column',
          justifyContent: 'center',
          minHeight: '220px'
        }}>
          <div style={{ display: 'flex', gap: '2px', marginBottom: '6px' }}>
            <Star size={18} fill="#ff9f1c" stroke="#ff9f1c" />
            <Star size={18} fill="#ff9f1c" stroke="#ff9f1c" />
            <Star size={18} fill="#ff9f1c" stroke="#ff9f1c" />
            <Star size={18} fill="#ff9f1c" stroke="#ff9f1c" />
            <div style={{ position: 'relative', display: 'inline-block', width: '18px', height: '18px' }}>
              <Star size={18} stroke="#ff9f1c" fill="none" style={{ position: 'absolute' }} />
              <div style={{ width: '90%', overflow: 'hidden', position: 'absolute' }}>
                <Star size={18} fill="#ff9f1c" stroke="#ff9f1c" />
              </div>
            </div>
          </div>

          <div style={{ fontSize: '1rem', fontWeight: '800', color: 'var(--blue)', marginBottom: '18px' }}>
            4.9 out of 5
          </div>

          <div style={{ display: 'flex', flexDirection: 'column', gap: '10px' }}>
            <div style={{ display: 'flex', alignItems: 'center', gap: '10px', fontSize: '0.82rem', fontWeight: '700', color: 'var(--blue)' }}>
              <span style={{ width: '40px' }}>5 star</span>
              <div style={{ flex: 1, height: '8px', backgroundColor: '#f1f5f9', borderRadius: '4px', overflow: 'hidden' }}>
                <div style={{ width: '90%', height: '100%', backgroundColor: '#003c71' }}></div>
              </div>
              <span style={{ width: '30px', textAlign: 'right' }}>90%</span>
            </div>
            <div style={{ display: 'flex', alignItems: 'center', gap: '10px', fontSize: '0.82rem', fontWeight: '700', color: 'var(--blue)' }}>
              <span style={{ width: '40px' }}>4 star</span>
              <div style={{ flex: 1, height: '8px', backgroundColor: '#f1f5f9', borderRadius: '4px', overflow: 'hidden' }}>
                <div style={{ width: '10%', height: '100%', backgroundColor: '#003c71' }}></div>
              </div>
              <span style={{ width: '30px', textAlign: 'right' }}>10%</span>
            </div>
            <div style={{ display: 'flex', alignItems: 'center', gap: '10px', fontSize: '0.82rem', fontWeight: '700', color: 'var(--blue)' }}>
              <span style={{ width: '40px' }}>3 star</span>
              <div style={{ flex: 1, height: '8px', backgroundColor: '#f1f5f9', borderRadius: '4px', overflow: 'hidden' }}>
                <div style={{ width: '0%', height: '100%', backgroundColor: '#003c71' }}></div>
              </div>
              <span style={{ width: '30px', textAlign: 'right' }}>0%</span>
            </div>
          </div>
        </div>

        <div style={{
          flex: '1',
          minWidth: 'min(600px, 100%)',
          position: 'relative',
          display: 'flex',
          alignItems: 'center',
          overflow: 'hidden'
        }}>
          <button 
            onClick={prevReview}
            style={{
              position: 'absolute',
              left: '10px',
              zIndex: 10,
              width: '40px',
              height: '40px',
              borderRadius: '50%',
              backgroundColor: '#ffffff',
              border: '1px solid var(--line)',
              boxShadow: 'var(--shadow-md)',
              display: 'flex',
              alignItems: 'center',
              justifyContent: 'center',
              cursor: 'pointer',
              color: 'var(--blue)',
              outline: 'none'
            }}
          >
            <ChevronLeft size={20} strokeWidth={2.5} />
          </button>

          <div style={{ width: '100%', overflow: 'hidden', padding: '10px 0' }}>
            <div style={{
              display: 'flex',
              gap: '20px',
              transition: transitionEnabled ? 'transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)' : 'none',
              transform: `translateX(calc(-${currentIndex * (100 / 3)}% - ${currentIndex * (40 / 3)}px))`,
              width: '100%'
            }}>
              {extendedReviews.map((review, idx) => (
                <div 
                  key={idx}
                  style={{
                    flex: '0 0 calc((100% - 40px) / 3)',
                    backgroundColor: '#ffffff',
                    border: '1px solid var(--line)',
                    borderRadius: '16px',
                    padding: '24px 20px',
                    boxShadow: 'var(--shadow-sm)',
                    display: 'flex',
                    flexDirection: 'column',
                    justifyContent: 'space-between',
                    minHeight: '220px',
                    boxSizing: 'border-box'
                  }}
                >
                  <div style={{ display: 'flex', alignItems: 'center', gap: '14px', marginBottom: '14px' }}>
                    <div style={{
                      width: '48px',
                      height: '48px',
                      borderRadius: '50%',
                      border: '2px solid var(--blue)',
                      display: 'flex',
                      alignItems: 'center',
                      justifyContent: 'center',
                      overflow: 'hidden',
                      backgroundColor: '#f8fafc',
                      flexShrink: 0
                    }}>
                      <svg viewBox="0 0 100 100" width="38" height="38" style={{ fill: '#000000', marginTop: '6px' }}>
                        <circle cx="50" cy="35" r="18" />
                        <path d="M15 85 C15 62, 25 55, 40 55 L45 65 L50 58 L55 65 L60 55 C75 55, 85 62, 85 85 Z" />
                        <polygon points="45,55 50,75 55,55" style={{ fill: '#ffffff' }} />
                        <rect x="48" y="55" width="4" height="25" style={{ fill: '#000000' }} />
                      </svg>
                    </div>

                    <div style={{ display: 'flex', flexDirection: 'column', gap: '4px', textAlign: 'left' }}>
                      <span style={{ fontWeight: '800', fontSize: '1rem', color: 'var(--blue)', lineHeight: 1.1 }}>
                        {review.name}
                      </span>
                      {renderStars(review.rating)}
                    </div>
                  </div>

                  <p style={{
                    fontSize: '0.86rem',
                    lineHeight: '1.5',
                    color: 'var(--blue)',
                    fontWeight: '600',
                    margin: 0,
                    textAlign: 'left',
                    display: '-webkit-box',
                    WebkitLineClamp: 5,
                    WebkitBoxOrient: 'vertical',
                    overflow: 'hidden',
                    flexGrow: 1
                  }}>
                    {review.text}
                  </p>
                </div>
              ))}
            </div>
          </div>

          <button 
            onClick={nextReview}
            style={{
              position: 'absolute',
              right: '10px',
              zIndex: 10,
              width: '40px',
              height: '40px',
              borderRadius: '50%',
              backgroundColor: '#ffffff',
              border: '1px solid var(--line)',
              boxShadow: 'var(--shadow-md)',
              display: 'flex',
              alignItems: 'center',
              justifyContent: 'center',
              cursor: 'pointer',
              color: 'var(--blue)',
              outline: 'none'
            }}
          >
            <ChevronRight size={20} strokeWidth={2.5} />
          </button>
        </div>
      </div>
    </div>
  );
};

export default HomepageReviews;
