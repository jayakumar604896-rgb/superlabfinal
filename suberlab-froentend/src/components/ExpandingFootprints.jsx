
const ExpandingFootprints = () => {
  const footprints = [
    {
      value: '15',
      title: 'NABL',
      subtitle: 'Accredited Labs',
      theme: 'orange-light' // alternating colors
    },
    {
      value: '60+',
      title: 'Cities',
      subtitle: 'of Operation',
      theme: 'blue-dark'
    },
    {
      value: '47',
      title: 'Test',
      subtitle: 'Processing Labs',
      theme: 'orange-light'
    },
    {
      value: '1400+',
      title: 'Collection',
      subtitle: 'Touchpoints',
      theme: 'blue-dark'
    },
    {
      value: '2700+',
      title: 'Tests',
      subtitle: 'in Portfolio',
      theme: 'orange-light'
    },
    {
      value: '14000+',
      title: 'Tests',
      subtitle: 'Conducted Everyday',
      theme: 'blue-dark'
    }
  ];

  return (
    <section className="footprints-section">
      <div className="footprints-container">
        <h2 className="footprints-main-title">Our Expanding Footprints</h2>
        
        <div className="footprints-grid">
          {footprints.map((item, index) => (
            <div key={index} className={`footprint-circle-card ${item.theme}`}>
              <div className="footprint-circle-content">
                <span className="footprint-value">{item.value}</span>
                <div className="footprint-divider"></div>
                <span className="footprint-title">{item.title}</span>
                <span className="footprint-subtitle">{item.subtitle}</span>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default ExpandingFootprints;
