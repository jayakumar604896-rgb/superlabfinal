import { useCallback, useEffect, useMemo, useState } from 'react';
import { ChevronLeft, ChevronRight, Star } from 'lucide-react';
import { fetchServiceReviews, getSuperlabCustomer, submitServiceReview } from '../services/api';

const ReviewsSection = ({
  serviceId = null,
  serviceSlug = null,
  packageId = null,
  packageSlug = null,
  itemName = 'this test',
}) => {
  const [reviews, setReviews] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [currentIndex, setCurrentIndex] = useState(0);
  const [transitionEnabled, setTransitionEnabled] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [formName, setFormName] = useState('');
  const [formRating, setFormRating] = useState(5);
  const [formComments, setFormComments] = useState('');
  const [submitLoading, setSubmitLoading] = useState(false);
  const [submitMessage, setSubmitMessage] = useState('');

  const reviewLabel = packageId || packageSlug ? 'package' : 'test';

  const loadReviews = useCallback(async () => {
    setIsLoading(true);
    const result = await fetchServiceReviews({ serviceId, serviceSlug, packageId, packageSlug });
    setReviews(result.data || []);
    setCurrentIndex(0);
    setIsLoading(false);
  }, [serviceId, serviceSlug, packageId, packageSlug]);

  useEffect(() => {
    loadReviews();
  }, [loadReviews]);

  useEffect(() => {
    const customer = getSuperlabCustomer();
    if (customer?.name && !formName) {
      setFormName(customer.name);
    }
  }, [formName]);

  const extendedReviews = reviews.length > 1
    ? [...reviews, ...reviews.slice(0, 2)]
    : reviews;

  const averageRating = useMemo(() => {
    if (!reviews.length) return 0;
    const total = reviews.reduce((sum, review) => sum + (review.rating || 0), 0);
    return Math.round((total / reviews.length) * 100) / 100;
  }, [reviews]);

  const ratingCounts = useMemo(() => {
    const counts = { 5: 0, 4: 0, 3: 0, 2: 0, 1: 0 };
    reviews.forEach((review) => {
      const rating = Math.min(5, Math.max(1, Number(review.rating) || 0));
      counts[rating] += 1;
    });
    return counts;
  }, [reviews]);

  const nextReview = useCallback(() => {
    if (!transitionEnabled || reviews.length <= 1) return;
    setCurrentIndex((prev) => prev + 1);
  }, [transitionEnabled, reviews.length]);

  const prevReview = () => {
    if (!transitionEnabled || reviews.length <= 1) return;
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
    if (reviews.length <= 1) return undefined;
    if (currentIndex === reviews.length) {
      const timer = setTimeout(() => {
        setTransitionEnabled(false);
        setCurrentIndex(0);
      }, 500);
      return () => clearTimeout(timer);
    }
    return undefined;
  }, [currentIndex, reviews.length]);

  useEffect(() => {
    if (!transitionEnabled) {
      const timer = setTimeout(() => setTransitionEnabled(true), 20);
      return () => clearTimeout(timer);
    }
    return undefined;
  }, [transitionEnabled]);

  useEffect(() => {
    if (reviews.length <= 1) return undefined;
    const timer = setInterval(nextReview, 4000);
    return () => clearInterval(timer);
  }, [nextReview, reviews.length]);

  const handlePostReview = async () => {
    if (!formName.trim() || !formComments.trim()) {
      alert('Please fill out both Name and Comments.');
      return;
    }

    if (formComments.trim().length < 10) {
      alert('Please write at least 10 characters in your review.');
      return;
    }

    setSubmitLoading(true);
    setSubmitMessage('');

    const payload = {
      reviewer_name: formName.trim(),
      rating: formRating,
      message: formComments.trim(),
    };

    if (packageId || packageSlug) {
      if (packageId) payload.package_id = packageId;
      else if (packageSlug) payload.package_slug = packageSlug;
    } else if (serviceSlug) {
      payload.service_slug = serviceSlug;
    } else if (serviceId) {
      payload.service_id = serviceId;
    }

    const result = await submitServiceReview(payload);
    setSubmitLoading(false);

    if (result.success) {
      setSubmitMessage(result.message || 'Thank you! Your review was submitted and will appear after approval.');
      setFormComments('');
      setFormRating(5);
      setShowForm(false);
      return;
    }

    setSubmitMessage(result.message || 'Could not submit your review.');
  };

  const renderStars = (rating) => (
    <div style={{ display: 'flex', gap: '2px' }}>
      {[1, 2, 3, 4, 5].map((s) => (
        <Star
          key={s}
          size={16}
          fill={s <= rating ? '#ff9f1c' : 'none'}
          stroke={s <= rating ? '#ff9f1c' : '#cbd5e1'}
          style={{ flexShrink: 0 }}
        />
      ))}
    </div>
  );

  const renderAverageStars = () => (
    <div style={{ display: 'flex', gap: '2px' }}>
      {[1, 2, 3, 4, 5].map((s) => (
        <Star
          key={s}
          size={18}
          fill={s <= Math.round(averageRating) ? '#ff9f1c' : 'none'}
          stroke={s <= Math.round(averageRating) ? '#ff9f1c' : '#cbd5e1'}
        />
      ))}
    </div>
  );

  return (
    <div className="page-section-container text-left" style={{ marginTop: '40px', marginBottom: '60px' }}>
      <div style={{ display: 'flex', gap: '30px', flexWrap: 'wrap', alignItems: 'stretch' }}>

        <div style={{
          flex: '0 0 280px',
          backgroundColor: '#ffffff',
          border: '1px solid var(--line)',
          borderRadius: '16px',
          padding: '24px',
          boxShadow: 'var(--shadow-sm)',
          display: 'flex',
          flexDirection: 'column',
          justifyContent: 'space-between',
          minHeight: '280px',
        }}>
          <div>
            <h3 style={{ fontSize: '1.2rem', fontWeight: '800', color: 'var(--blue)', margin: '0 0 8px 0' }}>
              Customer Reviews
            </h3>

            <div style={{ display: 'flex', alignItems: 'center', gap: '6px', marginBottom: '4px' }}>
              {reviews.length > 0 ? renderAverageStars() : renderStars(0)}
            </div>

            <div style={{ fontSize: '0.9rem', fontWeight: '700', color: 'var(--blue)', marginBottom: '20px' }}>
              {reviews.length > 0 ? `${averageRating} out of 5` : 'No ratings yet'}
              {reviews.length > 0 && (
                <span style={{ color: 'var(--muted)', fontWeight: '600' }}> · {reviews.length} review{reviews.length === 1 ? '' : 's'}</span>
              )}
            </div>

            <div style={{ display: 'flex', flexDirection: 'column', gap: '8px', marginBottom: '24px' }}>
              {[5, 4, 3].map((star) => {
                const count = ratingCounts[star] || 0;
                const width = reviews.length ? `${Math.round((count / reviews.length) * 100)}%` : '0%';
                return (
                  <div key={star} style={{ display: 'flex', alignItems: 'center', gap: '10px', fontSize: '0.82rem', fontWeight: '700', color: 'var(--blue)' }}>
                    <span style={{ width: '40px' }}>{star} star</span>
                    <div style={{ flex: 1, height: '8px', backgroundColor: '#f1f5f9', borderRadius: '4px', overflow: 'hidden' }}>
                      <div style={{ width, height: '100%', backgroundColor: '#ff9f1c' }} />
                    </div>
                  </div>
                );
              })}
            </div>
          </div>

          <div style={{ paddingBottom: showForm ? '10px' : '0' }}>
            <h4 style={{ fontSize: '0.95rem', fontWeight: '800', color: 'var(--blue)', margin: '0 0 2px 0' }}>
              Review this {reviewLabel}
            </h4>
            <p style={{ fontSize: '0.8rem', color: 'var(--muted)', margin: '0 0 12px 0' }}>
              Share your experience with {itemName}
            </p>

            {submitMessage && (
              <p style={{
                fontSize: '0.8rem',
                color: submitMessage.toLowerCase().includes('thank') || submitMessage.toLowerCase().includes('submitted')
                  ? '#166534'
                  : '#b91c1c',
                fontWeight: '600',
                margin: '0 0 10px 0',
              }}>
                {submitMessage}
              </p>
            )}

            <button
              type="button"
              style={{
                width: '100%',
                padding: '10px',
                border: '1px solid var(--blue)',
                borderRadius: '8px',
                backgroundColor: '#ffffff',
                color: 'var(--blue)',
                fontWeight: '700',
                fontSize: '0.85rem',
                cursor: 'pointer',
                marginBottom: showForm ? '12px' : '0',
              }}
              onClick={() => setShowForm(!showForm)}
            >
              {showForm ? 'Cancel' : 'Write a review'}
            </button>

            {showForm && (
              <div style={{ display: 'flex', flexDirection: 'column', gap: '12px', marginTop: '12px', textAlign: 'left' }}>
                <div style={{ display: 'flex', gap: '4px' }}>
                  {[1, 2, 3, 4, 5].map((s) => (
                    <Star
                      key={s}
                      size={24}
                      fill={s <= formRating ? '#ff9f1c' : 'none'}
                      stroke={s <= formRating ? '#ff9f1c' : '#cbd5e1'}
                      style={{ cursor: 'pointer' }}
                      onClick={() => setFormRating(s)}
                    />
                  ))}
                </div>

                <input
                  type="text"
                  placeholder="Name"
                  value={formName}
                  onChange={(e) => setFormName(e.target.value)}
                  style={{
                    width: '100%',
                    padding: '10px 12px',
                    border: '1px solid #cbd5e1',
                    borderRadius: '8px',
                    fontSize: '0.9rem',
                    boxSizing: 'border-box',
                  }}
                />

                <textarea
                  placeholder="Enter your comments"
                  value={formComments}
                  onChange={(e) => setFormComments(e.target.value)}
                  rows={4}
                  style={{
                    width: '100%',
                    padding: '10px 12px',
                    border: '1px solid #cbd5e1',
                    borderRadius: '8px',
                    fontSize: '0.9rem',
                    resize: 'none',
                    boxSizing: 'border-box',
                  }}
                />

                <button
                  type="button"
                  onClick={handlePostReview}
                  disabled={submitLoading}
                  style={{
                    width: '100%',
                    padding: '10px',
                    backgroundColor: '#003c71',
                    color: '#ffffff',
                    border: 'none',
                    borderRadius: '8px',
                    fontWeight: '700',
                    fontSize: '0.9rem',
                    cursor: submitLoading ? 'wait' : 'pointer',
                    opacity: submitLoading ? 0.7 : 1,
                  }}
                >
                  {submitLoading ? 'Submitting...' : 'Post'}
                </button>
              </div>
            )}
          </div>
        </div>

        <div style={{
          flex: '1',
          minWidth: 'min(500px, 100%)',
          position: 'relative',
          display: 'flex',
          alignItems: 'center',
          overflow: 'hidden',
          minHeight: '280px',
        }}>
          {isLoading ? (
            <div style={{ width: '100%', textAlign: 'center', color: 'var(--muted)', fontWeight: '600' }}>
              Loading reviews...
            </div>
          ) : reviews.length === 0 ? (
            <div style={{
              width: '100%',
              backgroundColor: '#ffffff',
              border: '1px dashed var(--line)',
              borderRadius: '16px',
              padding: '40px 24px',
              textAlign: 'center',
              color: 'var(--muted)',
            }}>
              No reviews yet for {itemName}. Be the first to share your experience.
            </div>
          ) : (
            <>
              {reviews.length > 1 && (
                <button type="button" onClick={prevReview} style={{
                  position: 'absolute', left: '10px', zIndex: 10, width: '40px', height: '40px',
                  borderRadius: '50%', backgroundColor: '#ffffff', border: '1px solid var(--line)',
                  boxShadow: 'var(--shadow-md)', display: 'flex', alignItems: 'center', justifyContent: 'center',
                  cursor: 'pointer', color: 'var(--blue)',
                }}>
                  <ChevronLeft size={20} strokeWidth={2.5} />
                </button>
              )}

              <div style={{ width: '100%', overflow: 'hidden', padding: '10px' }}>
                <div style={{
                  display: 'flex',
                  gap: '20px',
                  transition: transitionEnabled ? 'transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)' : 'none',
                  transform: reviews.length > 1
                    ? `translateX(calc(-${currentIndex * 50}% - ${currentIndex * 10}px))`
                    : 'none',
                  width: '100%',
                }}>
                  {extendedReviews.map((review, idx) => (
                    <div
                      key={`${review.id || idx}-${idx}`}
                      style={{
                        flex: reviews.length > 1 ? '0 0 calc(50% - 10px)' : '1 1 100%',
                        backgroundColor: '#ffffff',
                        border: '1px solid var(--line)',
                        borderRadius: '16px',
                        padding: '20px 24px',
                        boxShadow: 'var(--shadow-sm)',
                        boxSizing: 'border-box',
                      }}
                    >
                      <div style={{ display: 'flex', alignItems: 'center', gap: '14px', marginBottom: '12px' }}>
                        <div style={{
                          width: '44px', height: '44px', borderRadius: '50%', backgroundColor: '#e6f7f8',
                          display: 'flex', alignItems: 'center', justifyContent: 'center', color: 'var(--teal)',
                        }}>
                          {(review.name || '?').charAt(0).toUpperCase()}
                        </div>
                        <div>
                          <div style={{ fontWeight: '800', fontSize: '1rem', color: 'var(--blue)' }}>{review.name}</div>
                          {renderStars(review.rating)}
                        </div>
                      </div>
                      <p style={{ fontSize: '0.86rem', lineHeight: 1.5, color: '#475569', margin: 0, textAlign: 'left' }}>
                        {review.text}
                      </p>
                    </div>
                  ))}
                </div>
              </div>

              {reviews.length > 1 && (
                <button type="button" onClick={nextReview} style={{
                  position: 'absolute', right: '-20px', zIndex: 10, width: '40px', height: '40px',
                  borderRadius: '50%', backgroundColor: '#ffffff', border: '1px solid var(--line)',
                  boxShadow: 'var(--shadow-md)', display: 'flex', alignItems: 'center', justifyContent: 'center',
                  cursor: 'pointer', color: 'var(--blue)',
                }}>
                  <ChevronRight size={20} strokeWidth={2.5} />
                </button>
              )}
            </>
          )}
        </div>
      </div>
    </div>
  );
};

export default ReviewsSection;
