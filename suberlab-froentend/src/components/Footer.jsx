import React, { useState, useEffect } from 'react';
import AnimatedLogo from './AnimatedLogo';
import { fetchCategories, fetchServices, fetchFooterLocations, getSuperlabCustomer, submitEnquiry } from '../services/api';

const Footer = ({ isIsoModalOpen }) => {
  const [activeTab, setActiveTab] = useState('tests');
  const [dynamicCategories, setDynamicCategories] = useState([]);
  const [dynamicTests, setDynamicTests] = useState([]);
  const [dynamicLocations, setDynamicLocations] = useState([]);

  useEffect(() => {
    fetchCategories().then(({ data }) => {
      if (data && data.length > 0) {
        setDynamicCategories(data.map(c => c.name));
      }
    });
    fetchServices().then(({ data }) => {
      if (data && data.length > 0) {
        setDynamicTests(data.map(t => t.name));
      }
    });
    fetchFooterLocations().then(({ data }) => {
      if (data && data.length > 0) {
        setDynamicLocations(data);
      }
    });
  }, []);

  const defaultLocations = [
    { location_name: 'Lab Test in Delhi', map_link: 'https://maps.google.com/?q=Delhi' },
    { location_name: 'Lab Test in Gurgaon', map_link: 'https://maps.google.com/?q=Gurgaon' },
    { location_name: 'Lab Test in Noida', map_link: 'https://maps.google.com/?q=Noida' },
    { location_name: 'Lab Test in Ghaziabad', map_link: 'https://maps.google.com/?q=Ghaziabad' },
    { location_name: 'Lab Test in Pune', map_link: 'https://maps.google.com/?q=Pune' },
    { location_name: 'Lab Test in Mumbai', map_link: 'https://maps.google.com/?q=Mumbai' },
    { location_name: 'Lab Test in Bengaluru', map_link: 'https://maps.google.com/?q=Bengaluru' },
    { location_name: 'Lab Test in Dehradun', map_link: 'https://maps.google.com/?q=Dehradun' },
    { location_name: 'Lab Test in Faridabad', map_link: 'https://maps.google.com/?q=Faridabad' },
    { location_name: 'Lab Test in Thane', map_link: 'https://maps.google.com/?q=Thane' },
    { location_name: 'Lab Test in Manesar', map_link: 'https://maps.google.com/?q=Manesar' },
    { location_name: 'Lab Test in Zirakpur', map_link: 'https://maps.google.com/?q=Zirakpur' },
    { location_name: 'Lab Test in Greater Noida', map_link: 'https://maps.google.com/?q=Greater+Noida' },
    { location_name: 'Lab Test in Navi Mumbai', map_link: 'https://maps.google.com/?q=Navi+Mumbai' },
    { location_name: 'Lab Test in Pimpri Chinchwad', map_link: 'https://maps.google.com/?q=Pimpri+Chinchwad' },
    { location_name: 'Lab Test in Mohali', map_link: 'https://maps.google.com/?q=Mohali' },
    { location_name: 'Lab Test in Jaipur', map_link: 'https://maps.google.com/?q=Jaipur' },
    { location_name: 'Lab Test in Ahmedabad', map_link: 'https://maps.google.com/?q=Ahmedabad' },
    { location_name: 'Lab Test in Rohtak', map_link: 'https://maps.google.com/?q=Rohtak' },
    { location_name: 'Lab Test in Kolkata', map_link: 'https://maps.google.com/?q=Kolkata' },
    { location_name: 'Lab Test in Chennai', map_link: 'https://maps.google.com/?q=Chennai' },
    { location_name: 'Lab Test in Hyderabad', map_link: 'https://maps.google.com/?q=Hyderabad' },
    { location_name: 'Lab Test in Hoshiarpur', map_link: 'https://maps.google.com/?q=Hoshiarpur' },
    { location_name: 'Lab Test in Indore', map_link: 'https://maps.google.com/?q=Indore' },
    { location_name: 'Lab Test in Khanna', map_link: 'https://maps.google.com/?q=Khanna' },
    { location_name: 'Lab Test in Nashik', map_link: 'https://maps.google.com/?q=Nashik' },
    { location_name: 'Lab Test in Sirsa', map_link: 'https://maps.google.com/?q=Sirsa' },
    { location_name: 'Lab Test in Mathura', map_link: 'https://maps.google.com/?q=Mathura' },
    { location_name: 'Lab Test in Agra', map_link: 'https://maps.google.com/?q=Agra' },
    { location_name: 'Lab Test in Rudrapur', map_link: 'https://maps.google.com/?q=Rudrapur' },
    { location_name: 'Lab Test in Hisar', map_link: 'https://maps.google.com/?q=Hisar' },
    { location_name: 'Lab Test in Gohana', map_link: 'https://maps.google.com/?q=Gohana' },
    { location_name: 'Lab Test in Chandigarh', map_link: 'https://maps.google.com/?q=Chandigarh' },
    { location_name: 'Lab Test in Panchkula', map_link: 'https://maps.google.com/?q=Panchkula' },
    { location_name: 'Lab Test in Jalandhar', map_link: 'https://maps.google.com/?q=Jalandhar' },
    { location_name: 'Lab Test in Ludhiana', map_link: 'https://maps.google.com/?q=Ludhiana' },
    { location_name: 'Lab Test in Amritsar', map_link: 'https://maps.google.com/?q=Amritsar' },
    { location_name: 'Lab Test in Haridwar', map_link: 'https://maps.google.com/?q=Haridwar' },
    { location_name: 'Lab Test in Rishikesh', map_link: 'https://maps.google.com/?q=Rishikesh' },
    { location_name: 'Lab Test in Saharanpur', map_link: 'https://maps.google.com/?q=Saharanpur' },
    { location_name: 'Lab Test in Lucknow', map_link: 'https://maps.google.com/?q=Lucknow' },
    { location_name: 'Lab Test in Patna', map_link: 'https://maps.google.com/?q=Patna' },
    { location_name: 'Lab Test in Nagpur', map_link: 'https://maps.google.com/?q=Nagpur' },
    { location_name: 'Lab Test in Gwalior', map_link: 'https://maps.google.com/?q=Gwalior' },
    { location_name: 'Lab Test in Moradabad', map_link: 'https://maps.google.com/?q=Moradabad' },
    { location_name: 'Lab Test in Aligarh', map_link: 'https://maps.google.com/?q=Aligarh' },
    { location_name: 'Lab Test in Bathinda', map_link: 'https://maps.google.com/?q=Bathinda' },
    { location_name: 'Lab Test in Pathankot', map_link: 'https://maps.google.com/?q=Pathankot' }
  ];

  const tabContent = {
    tests: dynamicTests,
    categories: dynamicCategories,
    locations: dynamicLocations.length > 0 ? dynamicLocations : defaultLocations,
    checkups: [
      'Full Body Checkup in Delhi', 'Full Body Checkup in Gurgaon', 'Full Body Checkup in Noida', 'Full Body Checkup in Ghaziabad', 'Full Body Checkup in Pune', 'Full Body Checkup in Mumbai',
      'Full Body Checkup in Bengaluru', 'Full Body Checkup in Dehradun', 'Full Body Checkup in Faridabad', 'Full Body Checkup in Thane', 'Full Body Checkup in Manesar', 'Full Body Checkup in Zirakpur',
      'Full Body Checkup in Greater Noida', 'Full Body Checkup in Navi Mumbai', 'Full Body Checkup in Pimpri Chinchwad', 'Full Body Checkup in Mohali', 'Full Body Checkup in Jaipur',
      'Full Body Checkup in Ahmedabad', 'Full Body Checkup in Rohtak', 'Full Body Checkup in Kolkata', 'Full Body Checkup in Chennai', 'Full Body Checkup in Hyderabad', 'Full Body Checkup in Hoshiarpur',
      'Full Body Checkup in Indore', 'Full Body Checkup in Khanna', 'Full Body Checkup in Nashik', 'Full Body Checkup in Sirsa', 'Full Body Checkup in Mathura', 'Full Body Checkup in Agra', 'Full Body Checkup in Rudrapur',
      'Full Body Checkup in Hisar', 'Full Body Checkup in Gohana'
    ],
    links: [
      'Find Our Lab', 'Book A Test', 'Health Packages', 'Business', 'Speciality Test', 'Women Health Test', 'Organ Test', 'Contact Us', 'About Us',
      'Annual Return', 'Blog', 'Diseases', 'Symptoms', 'Diet Plan', 'Procedure Preparations', 'Calculators', 'BMI Calculator', 'Pregnancy',
      'Lifestyle Disease', 'Forum', 'Privacy Policy', 'Disclaimer', 'Terms & Conditions', 'Download Super Lab App', 'Sitemap', 'Virtual Labs'
    ]
  };

  const tabs = [
    { id: 'tests', label: 'Popular Tests' },
    { id: 'categories', label: 'Popular Categories' },
    { id: 'locations', label: 'Location' },
    { id: 'checkups', label: 'Full Body Checkup' },
    { id: 'links', label: 'Quick Links' }
  ];

  return (
    <footer className="footer-section">
      <div className="footer-container">
        
        {/* Footer Top Header: Brand, Socials, App Download */}
        <div className="footer-header-row">
          

          {/* Social Links */}
          <div className="footer-socials-col">
            <span className="footer-column-heading">Follow Us On</span>
            <div className="footer-social-icons">
              <a href="https://facebook.com" target="_blank" rel="noreferrer" className="social-icon-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
              </a>
              <a href="https://twitter.com" target="_blank" rel="noreferrer" className="social-icon-btn">
                {/* Custom X SVG */}
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                </svg>
              </a>
              <a href="https://linkedin.com" target="_blank" rel="noreferrer" className="social-icon-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
              </a>
              <a href="https://instagram.com" target="_blank" rel="noreferrer" className="social-icon-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
              </a>
            </div>
          </div>

          <div className="footer-app-col" style={{ display: 'flex', alignItems: 'center', justifyContent: 'flex-end' }}>
            <a href="#/" style={{ display: 'inline-flex', alignItems: 'center', gap: '10px', textDecoration: 'none' }}>
              <AnimatedLogo height={50} showText={false} dark={true} />
              <img 
                src="/footer-logo.png" 
                alt="SuperLab by Phlebee" 
                style={{ height: '80px', width: 'auto', objectFit: 'contain', marginLeft: '15px', scale: 1.7 }} 
              />
            </a>
          </div>
        </div>

        {/* Footer Middle Panel: Interactive Tabs and Linked Text */}
        <div className="footer-tabs-row">
          {/* Left Side Tab Navigation */}
          <div className="footer-tab-nav">
            {tabs.map((tab) => (
              <button 
                key={tab.id}
                className={`footer-tab-btn ${activeTab === tab.id ? 'active' : ''}`}
                onClick={() => setActiveTab(tab.id)}
                onMouseEnter={() => setActiveTab(tab.id)}
              >
                {tab.label}
                {activeTab === tab.id && <span className="tab-arrow-indicator">➔</span>}
              </button>
            ))}
          </div>

          {/* Right Side Scrollable Links Content */}
          <div className="footer-tab-content-panel">
            <div className="footer-tab-content-scroll">
              {tabContent[activeTab].map((item, idx) => {
                const isObject = typeof item === 'object' && item !== null;
                const label = isObject ? (item.location_name || item.name || '') : item;
                const mapLink = isObject ? item.map_link : null;

                return (
                  <React.Fragment key={idx}>
                    {mapLink ? (
                      <a
                        href={mapLink}
                        target="_blank"
                        rel="noreferrer"
                        className="footer-inline-item-link"
                        style={{ textDecoration: 'none', color: 'inherit' }}
                      >
                        {label}
                      </a>
                    ) : (
                      <span className="footer-inline-item-link">{label}</span>
                    )}
                    {idx < tabContent[activeTab].length - 1 && <span className="item-pipe-divider">|</span>}
                  </React.Fragment>
                );
              })}
            </div>
          </div>
        </div>

        {/* Bottom divider line */}
        <div className="footer-divider-line"></div>

        {/* Bottom copyrights row */}
        <div className="footer-bottom-row">
          <div className="footer-copy-text" style={{ display: 'flex', gap: '15px', alignItems: 'center', flexWrap: 'wrap' }}>
            <span>© 2026 Super Lab – Unit of Phlebee Healthcare Network Pvt Ltd.</span>
          </div>
          <div className="footer-bottom-brand-logo">
            <span className="footer-brand-logo-main">Super</span>
            <span className="footer-brand-logo-sub">Healthcare</span>
          </div>
        </div>

      </div>

      {/* Sticky Bottom Callback Action Bar */}
      {!isIsoModalOpen && (
        <div className="footer-sticky-bar">
          <div className="sticky-bar-container">
            <span className="sticky-bar-text">Get a Call Back from our Health Advisor</span>
            <button className="btn-sticky-callback" onClick={async () => {
              const customer = getSuperlabCustomer();
              if (customer && (customer.name || customer.mobile)) {
                try {
                  const response = await submitEnquiry({
                    name: customer.name || 'Logged-in Customer',
                    email: customer.email || null,
                    mobile: customer.mobile || '',
                    subject: 'Callback Request',
                    message: 'Customer requested a callback via footer action bar.'
                  });
                  if (response.success) {
                    alert('Callback request sent successfully! Our advisor will call you shortly.');
                  } else {
                    alert('Failed to request callback. Please try again.');
                  }
                } catch (err) {
                  console.error(err);
                  alert('An error occurred. Please try again.');
                }
              } else {
                if (window.location.hash === '#/' || window.location.hash === '') {
                  const formElement = document.querySelector('.quick-booking-panel');
                  if (formElement) {
                    formElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    const nameInput = formElement.querySelector('input[name="name"]');
                    if (nameInput) nameInput.focus();
                  }
                } else {
                  window.location.hash = '#/';
                  setTimeout(() => {
                    const formElement = document.querySelector('.quick-booking-panel');
                    if (formElement) {
                      formElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                      const nameInput = formElement.querySelector('input[name="name"]');
                      if (nameInput) nameInput.focus();
                    }
                  }, 300);
                }
              }
            }}>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" className="sticky-call-icon"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
              Call me now
            </button>
          </div>
        </div>
      )}

    </footer>
  );
};

export default Footer;
