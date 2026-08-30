

const HomeCollectionWorkflow = () => {
  return (
    <div className="workflow-section">
      <h2>How Does Home Sample Collection work?</h2>
      
      <div className="workflow-container">
        {/* Connector line between circles */}
        <div className="workflow-line" />

        {/* Steps Circles */}
        <div className="workflow-steps-row">
          {[
            {
              num: "01.",
              className: "workflow-circle step-1",
              text: "Select Your Test"
            },
            {
              num: "02.",
              className: "workflow-circle step-2",
              text: "Set Location, Pick Date & Time"
            },
            {
              num: "03.",
              className: "workflow-circle step-3",
              text: "Pay Securely"
            },
            {
              num: "04.",
              className: "workflow-circle step-4",
              text: "Our Expert Visits You"
            },
            {
              num: "05.",
              className: "workflow-circle step-5",
              text: "Sample Collected Securely"
            },
            {
              num: "06.",
              className: "workflow-circle step-6",
              text: "Tested & Analyzed at NABL Accredited Lab"
            },
            {
              num: "07.",
              className: "workflow-circle step-7",
              text: "Get Your Detailed Report Online"
            }
          ].map((step, idx) => (
            <div key={idx} className="workflow-step-item">
              <span className="workflow-step-num">{step.num}</span>
              <div className={step.className}>
                {step.icon}
              </div>
              {/* Tooltip popup */}
              <div className="workflow-tooltip">
                {step.text}
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
};

export default HomeCollectionWorkflow;
