import { useState, useEffect } from 'react';
import { ChevronLeft, ChevronRight, ShieldCheck, Clock, Award } from 'lucide-react';
import { submitEnquiry } from '../services/api';

const HeroSlider = () => {
  const [currentSlide, setCurrentSlide] = useState(0);
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    subject: ''
  });

  const slides = [
    {
      title: "Comprehensive Full Body Checkup",
      subtitle: "Get up to 70+ essential parameters checked at the comfort of your home. Reports within 24 hours.",
      cta: "Book Now",
      bgGradient: "linear-gradient(135deg, #0d5ead 0%, #083f78 100%)"
    },
    {
      title: "Accurate Diabetes Screening",
      subtitle: "Regular monitoring keeps HbA1c in check. Safe & hygienic sample collection by expert phlebotomists.",
      cta: "Book Test",
      bgGradient: "linear-gradient(135deg, #cc6200 0%, #f57c0b 100%)"
    },
    {
      title: "Advanced Diagnostic Radiology",
      subtitle: "Book CT Scans, MRI, Ultrasound, and X-Rays at your nearest partner lab with state-of-the-art tech.",
      cta: "Find Center",
      bgGradient: "linear-gradient(135deg, #102033 0%, #0d5ead 100%)"
    }
  ];

  // Auto-play slides
  useEffect(() => {
    const timer = setInterval(() => {
      setCurrentSlide((prev) => (prev + 1) % slides.length);
    }, 5000);
    return () => clearInterval(timer);
  }, [slides.length]);

  const prevSlide = () => {
    setCurrentSlide((prev) => (prev === 0 ? slides.length - 1 : prev - 1));
  };

  const nextSlide = () => {
    setCurrentSlide((prev) => (prev + 1) % slides.length);
  };

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({ ...prev, [name]: value }));
  };

  const handleBookingSubmit = async (e) => {
    e.preventDefault();
    try {
      const response = await submitEnquiry({
        name: formData.name,
        email: formData.email || null,
        mobile: formData.phone,
        subject: formData.subject || 'Callback Request',
        message: 'Customer requested a callback.'
      });

      if (response.success) {
        alert(response.message || `Thank you, ${formData.name}! Your request has been sent.`);
      } else {
        alert('Failed to send callback request. Please try again.');
      }
    } catch (error) {
      console.error('Error submitting callback request:', error);
      alert('An error occurred. Please try again.');
    }
    setFormData({ name: '', email: '', phone: '', subject: '' });
  };

  return (
    <section className="hero-section">
      <div className="hero-container">
        {/* Slider */}
        <div className="hero-slider">
          {slides.map((slide, index) => (
            <div
              key={index}
              className={`slide ${index === currentSlide ? 'active' : ''}`}
            >
              <div
                className="slide-placeholder-bg"
                style={{ background: slide.bgGradient }}
              />
              <div className="slide-overlay" />
              <div className="slide-content-wrapper">
                <h1 className="slide-title">{slide.title}</h1>
                <p className="slide-subtitle">{slide.subtitle}</p>
                <div className="slide-features">
                  <div className="slide-feature-item">
                    <span className="feature-icon-wrapper">
                      <ShieldCheck size={18} />
                    </span>
                    <span>NABL Accredited Partner Labs</span>
                  </div>
                  <div className="slide-feature-item">
                    <span className="feature-icon-wrapper">
                      <Clock size={18} />
                    </span>
                    <span>100% Digital & Smart Reports in 24 hrs</span>
                  </div>
                  <div className="slide-feature-item">
                    <span className="feature-icon-wrapper">
                      <Award size={18} />
                    </span>
                    <span>Expert & Certified Home Phlebotomists</span>
                  </div>
                </div>
                <button className="slide-cta" onClick={() => alert(`Redirecting to: ${slide.title}`)}>
                  {slide.cta}
                </button>
              </div>
            </div>
          ))}

          {/* Navigation Arrows */}
          <button className="slider-arrow slider-arrow-left" onClick={prevSlide}>
            <ChevronLeft size={24} />
          </button>
          <button className="slider-arrow slider-arrow-right" onClick={nextSlide}>
            <ChevronRight size={24} />
          </button>

          {/* Dots Indicator */}
          <div className="slider-dots">
            {slides.map((_, index) => (
              <button
                key={index}
                className={`slider-dot ${index === currentSlide ? 'active' : ''}`}
                onClick={() => setCurrentSlide(index)}
              />
            ))}
          </div>
        </div>

        {/* Quick Booking Widget */}
        <div className="quick-booking-panel">
          <h3 className="quick-booking-title">Quick Home Collection</h3>
          <p className="quick-booking-subtitle">Fill the form, our team will contact you in 15 minutes.</p>
          <form className="booking-form" onSubmit={handleBookingSubmit}>
            <div className="form-group">
              <label className="form-label">Patient Name</label>
              <input
                type="text"
                name="name"
                className="form-input"
                placeholder="Enter full name"
                value={formData.name}
                onChange={handleInputChange}
                required
              />
            </div>
            
            <div className="form-group">
              <label className="form-label">Email Address</label>
              <input
                type="email"
                name="email"
                className="form-input"
                placeholder="Enter email address"
                value={formData.email}
                onChange={handleInputChange}
              />
            </div>

            <div className="form-group">
              <label className="form-label">Phone Number</label>
              <input
                type="tel"
                name="phone"
                className="form-input"
                placeholder="Enter 10-digit number"
                pattern="[0-9]{10}"
                value={formData.phone}
                onChange={handleInputChange}
                required
              />
            </div>

            <div className="form-group">
              <label className="form-label">Subject</label>
              <textarea
                name="subject"
                className="form-textarea"
                placeholder="Enter subject/details"
                value={formData.subject}
                onChange={handleInputChange}
                required
              />
            </div>

            <button type="submit" className="btn-book-submit">
              Request Callback
            </button>
          </form>
        </div>
      </div>
    </section>
  );
};

export default HeroSlider;
