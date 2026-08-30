

const StaticLogo = ({ height = 40}) => {

  return (
    <div 
      className="static-logo-container"
      style={{ 
        display: 'inline-flex', 
        alignItems: 'center', 
        gap: '10px', 
        height: height, 
        userSelect: 'none',
        position: 'relative',
        overflow: 'visible'
      }}
    >
     
      <div style={{ position: 'relative', height: height, display: 'flex', alignItems: 'center', flexShrink: 0 }}>
        <img src="/super-lab-logo.jpeg" alt="Super Lab" style={{ height: '100%', width: 'auto', objectFit: 'contain', scale: '2.4', marginLeft: '15px' }} />
        {/* <img src="/super-lab-logo.png" alt="Super Lab" style={{ height: '100%', width: 'auto', objectFit: 'contain', scale: '1.7' }} /> */}
      </div>
    </div>
  );
};

export default StaticLogo;
