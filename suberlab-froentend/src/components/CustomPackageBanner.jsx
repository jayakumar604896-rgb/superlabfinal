

const CustomPackageBanner = () => {
  return (
    <section className="custom-package-section">
      <div className="custom-package-container">
        <a href="#/make-package" className="custom-package-banner-link">
          <div className="custom-package-banner-content">
            
            {/* Left Column: Title, Subtitle, Categories, Button */}
            <div className="banner-left-content">
              <div className="banner-header-group">
                <h2 className="banner-main-title">Make Your Own Package</h2>
                <div className="banner-title-line"></div>
                <p className="banner-sub-title">Create a package as per your needs</p>
              </div>

              {/* Bordered Categories Container */}
              <div className="banner-categories-box">
                
                {/* CBC */}
                <div className="banner-category-item">
                  <div className="banner-category-icon-box">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M8 19C10.2 19 12 17.2 12 15C12 12 8 8 8 8C8 8 4 12 4 15C4 17.2 5.8 19 8 19Z" fill="#ff3b3b" opacity="0.8"/>
                      <path d="M15 21C17.8 21 20 18.8 20 16C20 12.5 15 7 15 7C15 7 10 12.5 10 16C10 18.8 12.2 21 15 21Z" fill="#ff1f1f"/>
                      <circle cx="13.5" cy="14.5" r="1" fill="white" opacity="0.8"/>
                    </svg>
                  </div>
                  <span className="banner-category-label">CBC</span>
                </div>

                {/* Urine Test */}
                <div className="banner-category-item">
                  <div className="banner-category-icon-box">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M6 3H14V5H6V3Z" fill="none" stroke="white" strokeWidth="1.5"/>
                      <path d="M7 5H13V15C13 16.6569 11.6569 18 10 18C8.34315 18 7 16.6569 7 15V5Z" fill="rgba(255,255,255,0.05)" stroke="white" strokeWidth="1.5"/>
                      <path d="M8 12H12V15C12 16.1046 11.1046 17 10 17C8.89543 17 8 16.1046 8 15V12Z" fill="#eab308"/>
                      <rect x="9.5" y="2" width="1" height="11" rx="0.5" fill="white"/>
                      <rect x="9.5" y="5" width="1" height="1.5" fill="#f97316"/>
                      <rect x="9.5" y="8" width="1" height="1.5" fill="#3b82f6"/>
                    </svg>
                  </div>
                  <span className="banner-category-label">Urine Test</span>
                </div>

                {/* Diabetes */}
                <div className="banner-category-item">
                  <div className="banner-category-icon-box">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M12 3C12 3 6 10 6 15C6 18.3 8.7 21 12 21C15.3 21 18 18.3 18 15C18 10 12 3 12 3Z" stroke="#ff3b3b" strokeWidth="2" fill="none"/>
                      <circle cx="12" cy="15" r="3" stroke="#ff3b3b" strokeWidth="1.5" fill="none"/>
                    </svg>
                  </div>
                  <span className="banner-category-label">Diabetes</span>
                </div>

                {/* Heart */}
                <div className="banner-category-item">
                  <div className="banner-category-icon-box">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M12 21C12 21 5 14 5 9.5C5 6.46243 7.46243 4 10.5 4C12 4 13 5 13 5L12 7.5L14.5 9.5L16.5 7.5L16.5 4.5C19.5 4.5 21.5 6.46243 21.5 9.5C21.5 14 12 21 12 21Z" stroke="#ff3b3b" strokeWidth="1.8" fill="none"/>
                      <path d="M11.5 4.5V2" stroke="#ff3b3b" strokeWidth="1.5" strokeLinecap="round"/>
                      <path d="M14.5 4V1.5" stroke="#ff3b3b" strokeWidth="1.5" strokeLinecap="round"/>
                    </svg>
                  </div>
                  <span className="banner-category-label">Heart</span>
                </div>

                {/* Kidney */}
                <div className="banner-category-item">
                  <div className="banner-category-icon-box">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M8.5 7C6.5 7 5 9 5 12C5 15 6.5 17 8.5 17C9.5 17 10 16 10 14.5C10 13 9 12 9 12C9 12 10 11 10 9.5C10 8 9.5 7 8.5 7Z" stroke="white" strokeWidth="1.5" fill="none"/>
                      <path d="M15.5 7C17.5 7 19 9 19 12C19 15 17.5 17 15.5 17C14.5 17 14 16 14 14.5C14 13 15 12 15 12C15 12 14 11 14 9.5C14 8 14.5 7 15.5 7Z" stroke="white" strokeWidth="1.5" fill="none"/>
                    </svg>
                  </div>
                  <span className="banner-category-label">Kidney</span>
                </div>

                {/* Liver */}
                <div className="banner-category-item">
                  <div className="banner-category-icon-box">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M20 7C14 5 7.5 9.5 4 10.5C3.5 10.7 3 11.2 3 11.8C3 13.5 4.5 18 10 18C15 18 20 14 21 11C21.5 9.5 21.5 7.5 20 7Z" stroke="white" strokeWidth="1.5" fill="none"/>
                    </svg>
                  </div>
                  <span className="banner-category-label">Liver</span>
                </div>

                {/* Bone Health */}
                <div className="banner-category-item">
                  <div className="banner-category-icon-box">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M12 3V10.5M12 3C11 3 10 4 10 5C10 6 11 6.5 12 6.5C13 6.5 14 6 14 5C14 4 13 3 12 3Z" stroke="white" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
                      <path d="M12 21V13.5M12 21C13 21 14 20 14 19C14 18 13 17.5 12 17.5C11 17.5 10 18 10 19C10 20 11 21 12 21Z" stroke="white" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
                      <path d="M8 12C9 10.5 10.5 10 12 10C13.5 10 15 10.5 16 12" stroke="white" strokeWidth="1.5"/>
                      <path d="M8 12C9 13.5 10.5 14 12 14C13.5 14 15 13.5 16 12" stroke="white" strokeWidth="1.5"/>
                    </svg>
                  </div>
                  <span className="banner-category-label">Bone Health</span>
                </div>

                {/* Thyroid */}
                <div className="banner-category-item">
                  <div className="banner-category-icon-box">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M6 7C6 7 4 9 4 12C4 16 7 18 9 18C10 18 11 16.5 11 15C11 13.5 10 12.5 10 11C10 9 11.5 7 12 7C12.5 7 14 9 14 11C14 12.5 13 13.5 13 15C13 16.5 14 18 15 18C17 18 20 16 20 12C20 9 18 7 18 7C18 7 17.5 11 15 11C13.5 11 12.5 10 12 9.5C11.5 10 10.5 11 9 11C6.5 11 6 7 6 7Z" stroke="white" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" fill="none"/>
                    </svg>
                  </div>
                  <span className="banner-category-label">Thyroid</span>
                </div>

                {/* CRP */}
                <div className="banner-category-item">
                  <div className="banner-category-icon-box">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <circle cx="12" cy="6" r="3" stroke="white" strokeWidth="1.5"/>
                      <path d="M6 20V17C6 14.7909 7.79086 13 10 13H14C16.2091 13 18 14.7909 18 17V20" stroke="white" strokeWidth="1.5" strokeLinecap="round"/>
                      <circle cx="12" cy="15" r="2" fill="#ff4d4d"/>
                    </svg>
                  </div>
                  <span className="banner-category-label">CRP</span>
                </div>

                {/* & More */}
                <div className="banner-category-item banner-more-item">
                  <span className="banner-category-more-text">& More</span>
                </div>

              </div>

              {/* Start Now Button (Centered below the box) */}
              <div className="banner-action-row">
                <span className="btn-start-now">Start Now</span>
              </div>
            </div>

            {/* Right Column: Save Badge */}
            <div className="banner-right-content">
              <fieldset className="save-badge-fieldset">
                <legend className="save-badge-legend">SAVE UP TO</legend>
                <span className="save-badge-percent">25%</span>
              </fieldset>
            </div>

          </div>
        </a>
      </div>
    </section>
  );
};

export default CustomPackageBanner;
