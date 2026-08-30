
import { navigateToLabTestsFilter } from '../utils/labTestsNavigation';

const LifestyleDiseaseSlider = () => {
  const diseases = [
    { name: 'Diabetes', img: '/lifestyle/diabetes.png' },
    { name: 'Depression', img: '/lifestyle/Depression.png' },
    { name: 'Fatigue', img: '/lifestyle/Fatigue.png' },
    { name: 'Hypertension', img: '/lifestyle/Hypertension.png' },
    { name: 'Obesity', img: '/lifestyle/Obesity.png' }
  ];

  return (
    <section className="vital-organs-section lifestyle-disease-section">
      <div className="vital-organs-container">
        
        {/* Header Row */}
        <div className="section-header-row">
          <h2 className="section-main-title">Lifestyle Disease</h2>
          <button className="btn-view-all-packages" onClick={() => navigateToLabTestsFilter('Lifestyle Disease')}>
            View All
          </button>
        </div>

        {/* Static horizontal flex container */}
        <div className="lifestyle-static-grid">
          {diseases.map((disease, index) => (
            <button
              key={index}
              type="button"
              className="vital-organ-card"
              onClick={() => navigateToLabTestsFilter(disease.name)}
              style={{ background: 'none', border: 'none', cursor: 'pointer', padding: 0 }}
            >
              <div className="vital-organ-circle">
                <img 
                  src={disease.img} 
                  alt={disease.name} 
                  className="vital-organ-img"
                  onError={(e) => {
                    e.target.style.display = 'none';
                  }}
                />
              </div>
              <span className="vital-organ-name">{disease.name}</span>
            </button>
          ))}
        </div>

      </div>
    </section>
  );
};

export default LifestyleDiseaseSlider;
