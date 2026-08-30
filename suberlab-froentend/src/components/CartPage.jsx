import { useState, useEffect } from 'react';
import { 
  Trash2, 
  ShieldCheck, 
  ArrowLeft,
  CheckCircle,
  AlertCircle,
} from 'lucide-react';
import { submitBooking, getSuperlabCustomer, getActivePaymentGateways, createUnifiedPaymentOrder, verifyUnifiedPayment, validateCoupon, fetchAvailableCoupons } from '../services/api';
import { getOriginalPrice, getSalePrice, cartHasCustomPackage } from '../utils/pricing';

const CartPage = () => {
  // Read and listen to the global cart state
  const [cartItems, setCartItems] = useState(() => {
    return window.getSuperlabCart ? window.getSuperlabCart() : [];
  });

  useEffect(() => {
    const handleCartUpdate = () => {
      if (window.getSuperlabCart) {
        setCartItems(window.getSuperlabCart());
      }
    };
    window.addEventListener('superlab_cart_update', handleCartUpdate);
    return () => window.removeEventListener('superlab_cart_update', handleCartUpdate);
  }, []);

  // Form states
  const [patientName, setPatientName] = useState('');
  const [patientPhone, setPatientPhone] = useState('');
  const [patientAge, setPatientAge] = useState('');
  const [patientGender, setPatientGender] = useState('Male');
  const [couponCode, setCouponCode] = useState('');
  const [appliedCoupon, setAppliedCoupon] = useState(null);
  const [couponError, setCouponError] = useState('');
  const [couponLoading, setCouponLoading] = useState(false);
  const [availableCoupons, setAvailableCoupons] = useState([]);
  const [availableCouponsLoading, setAvailableCouponsLoading] = useState(false);
  const [confirmedTotal, setConfirmedTotal] = useState(0);
  const [checkoutStep, setCheckoutStep] = useState('cart'); // cart, checkout, success
  
  // Payment states
  const [isPaymentModalOpen, setIsPaymentModalOpen] = useState(false);
  const [isGuestPayment, setIsGuestPayment] = useState(false);
  const [paymentLoading, setPaymentLoading] = useState(false);
  const [paymentSuccess, setPaymentSuccess] = useState(false);
  const [gateways, setGateways] = useState([]);
  const [selectedGateway, setSelectedGateway] = useState('razorpay');

  useEffect(() => {
    let cancelled = false;
    const loadGateways = async () => {
      const result = await getActivePaymentGateways();
      if (!cancelled) {
        setGateways(result || []);
        if (result && result.length > 0) {
          setSelectedGateway(result[0].slug);
        }
      }
    };
    loadGateways();
    return () => { cancelled = true; };
  }, []);

  const customer = getSuperlabCustomer();
  const isLoggedIn = !!(customer && (customer.name || customer.mobile));
  const couponsDisabled = cartHasCustomPackage(cartItems);

  const linePrice = (item) => getSalePrice(item);
  const lineOriginal = (item) => getOriginalPrice(item);
  const getItemsSubtotal = () => Math.round(cartItems.reduce((acc, item) => acc + linePrice(item), 0));
  const getCatalogSavings = () => Math.round(cartItems.reduce((acc, item) => {
    const original = lineOriginal(item);
    return acc + (original ? Math.max(0, original - linePrice(item)) : 0);
  }, 0));
  const getCouponDiscount = () => appliedCoupon?.discount_amount || 0;
  const getGrandTotal = () => {
    const subtotal = getItemsSubtotal();
    if (appliedCoupon && Number(appliedCoupon.subtotal) === subtotal && appliedCoupon.final_total != null) {
      return appliedCoupon.final_total;
    }
    return Math.max(0, subtotal - getCouponDiscount());
  };

  const syncAppliedCoupon = async (code, subtotal, { clearOnFailure = true } = {}) => {
    const result = await validateCoupon(code, subtotal);
    if (result.success) {
      setAppliedCoupon(result.data);
      setCouponCode(result.data.code);
      setCouponError('');
      return result.data;
    }

    if (clearOnFailure) {
      setAppliedCoupon(null);
      setCouponCode('');
    }
    setCouponError(result.message || 'Invalid coupon code.');
    return null;
  };

  useEffect(() => {
    if (!couponsDisabled) return undefined;

    setAppliedCoupon(null);
    setCouponCode('');
    setCouponError('');
    setAvailableCoupons([]);
    return undefined;
  }, [couponsDisabled]);

  useEffect(() => {
    const code = appliedCoupon?.code;
    if (couponsDisabled || cartItems.length === 0) {
      if (appliedCoupon) {
        setAppliedCoupon(null);
        setCouponCode('');
        setCouponError('');
      }
      return undefined;
    }
    if (!code) return undefined;

    let cancelled = false;
    const subtotal = getItemsSubtotal();

    (async () => {
      const result = await validateCoupon(code, subtotal);
      if (cancelled) return;

      if (result.success) {
        setAppliedCoupon(result.data);
        setCouponError('');
      } else {
        setAppliedCoupon(null);
        setCouponCode('');
        setCouponError(result.message || 'Coupon removed because your cart changed.');
      }
    })();

    return () => {
      cancelled = true;
    };
  }, [cartItems, couponsDisabled]);

  const ensureFreshCoupon = async () => {
    if (couponsDisabled) return true;
    if (!appliedCoupon?.code) return true;
    const refreshed = await syncAppliedCoupon(appliedCoupon.code, getItemsSubtotal(), { clearOnFailure: true });
    return !!refreshed;
  };

  const buildCheckoutPayload = (extra = {}) => ({
    ...extra,
    subtotal: getItemsSubtotal(),
    total_price: getGrandTotal(),
    coupon_code: couponsDisabled ? null : (appliedCoupon?.code || null),
    items: cartItems.map(item => ({
      id: item.id,
      name: item.name,
      price: linePrice(item),
      category: item.category,
      type: item.type,
      is_custom: item.isCustom === true,
      custom_package_id: item.customPackageId || null,
    })),
  });

  const handleRemoveItem = (id) => {
    const key = window.getSuperlabCartKey ? window.getSuperlabCartKey() : 'superlab_cart_guest';
    const updated = cartItems.filter(item => item.id !== id);
    localStorage.setItem(key, JSON.stringify(updated));
    window.dispatchEvent(new Event('superlab_cart_update'));
  };
  useEffect(() => {
    let cancelled = false;

    const loadAvailableCoupons = async () => {
      if (cartHasCustomPackage(window.getSuperlabCart ? window.getSuperlabCart() : [])) {
        setAvailableCoupons([]);
        setAvailableCouponsLoading(false);
        return;
      }

      setAvailableCouponsLoading(true);
      const result = await fetchAvailableCoupons();
      if (!cancelled) {
        setAvailableCoupons(result.data || []);
        setAvailableCouponsLoading(false);
      }
    };

    loadAvailableCoupons();
    window.addEventListener('superlab_auth_update', loadAvailableCoupons);

    return () => {
      cancelled = true;
      window.removeEventListener('superlab_auth_update', loadAvailableCoupons);
    };
  }, [couponsDisabled]);

  const handleApplyCoupon = async (codeOverride = null) => {
    if (couponsDisabled) return;

    const code = (codeOverride ?? couponCode).trim();
    if (!code) {
      setCouponError('Enter a coupon code.');
      return;
    }

    setCouponLoading(true);
    setCouponError('');
    setCouponCode(code.toUpperCase());

    const refreshed = await syncAppliedCoupon(code, getItemsSubtotal(), { clearOnFailure: true });
    setCouponLoading(false);

    if (!refreshed) {
      setAppliedCoupon(null);
    }
  };

  const handleRemoveCoupon = () => {
    setAppliedCoupon(null);
    setCouponCode('');
    setCouponError('');
  };
  const handleBNPLCheckout = async (isGuest = false) => {
    if (isGuest && (!patientName || !patientPhone || !patientAge)) {
      alert('Please fill out all patient fields.');
      return;
    }

    if (!(await ensureFreshCoupon())) return;
    
    const amount = getGrandTotal();
    
    try {
      let bookingDetails = {};
      if (isGuest) {
        bookingDetails = buildCheckoutPayload({
          name: patientName,
          mobile: patientPhone,
          age: patientAge,
          gender: patientGender,
          address: 'Guest Checkout',
          booking_date: new Date().toISOString().split('T')[0],
          payment_method: 'BNPL',
          payment_status: 'pending',
        });
      } else {
        const cust = getSuperlabCustomer();
        bookingDetails = buildCheckoutPayload({
          name: cust ? (cust.name || 'Logged-in Customer') : 'Online Customer',
          email: cust ? (cust.email || null) : null,
          mobile: cust ? cust.mobile : '',
          age: cust ? (cust.age || 30) : 30,
          gender: cust ? (cust.gender || 'Male') : 'Male',
          address: cust ? (cust.address || 'Profile Address') : 'Online Booking',
          booking_date: new Date().toISOString().split('T')[0],
          payment_method: 'BNPL',
          payment_status: 'pending',
        });
      }

      const response = await submitBooking(bookingDetails);
      if (response && (response.status === 'success' || response.success)) {
        setConfirmedTotal(amount);
        const key = window.getSuperlabCartKey ? window.getSuperlabCartKey() : 'superlab_cart_guest';
        localStorage.setItem(key, JSON.stringify([]));
        window.dispatchEvent(new Event('superlab_cart_update'));
        window.dispatchEvent(new Event('superlab_auth_update'));
        setCheckoutStep('success');
      } else {
        alert('Booking failed. Please try again.');
      }
    } catch (err) {
      console.error(err);
      alert('An error occurred during booking.');
    }
  };

  const loadPaymentScript = (src, globalVar) => {
    return new Promise((resolve) => {
      if (window[globalVar]) {
        resolve(true);
        return;
      }
      const script = document.createElement('script');
      script.src = src;
      script.onload = () => resolve(true);
      script.onerror = () => resolve(false);
      document.body.appendChild(script);
    });
  };

  const handlePayment = async (isGuest = false) => {
    if (isGuest && (!patientName || !patientPhone || !patientAge)) {
      alert('Please fill out all patient fields.');
      return;
    }

    if (!selectedGateway) {
      alert('Please select a payment gateway.');
      return;
    }

    if (selectedGateway === 'bnpl') {
      await handleBNPLCheckout(isGuest);
      return;
    }

    let sdkLoaded = true;
    if (selectedGateway === 'razorpay') {
      sdkLoaded = await loadPaymentScript('https://checkout.razorpay.com/v1/checkout.js', 'Razorpay');
    } else if (selectedGateway === 'stripe') {
      sdkLoaded = await loadPaymentScript('https://js.stripe.com/v3/', 'Stripe');
    }

    if (!sdkLoaded) {
      alert(`Failed to load payment SDK for ${selectedGateway}.`);
      return;
    }

    if (!(await ensureFreshCoupon())) return;

    const amount = getGrandTotal();

    try {
      const orderData = await createUnifiedPaymentOrder(selectedGateway, amount);
      if (!orderData || orderData.status !== 'success') {
        alert('Failed to initialize transaction. Please try again.');
        return;
      }

      if (selectedGateway === 'razorpay') {
        const options = {
          key: orderData.key_id,
          amount: orderData.amount * 100,
          currency: 'INR',
          name: orderData.company_name || 'SuperLab Diagnostics',
          description: 'Secure Laboratory Bookings',
          order_id: orderData.order_id,
          handler: async (response) => {
            await handleVerification(isGuest, amount, {
              gateway: 'razorpay',
              razorpay_payment_id: response.razorpay_payment_id,
              razorpay_order_id: response.razorpay_order_id,
              razorpay_signature: response.razorpay_signature || null,
              mock: !!orderData.mock,
            });
          },
          prefill: {
            name: isGuest ? patientName : (customer?.name || ''),
            contact: isGuest ? patientPhone : (customer?.mobile || ''),
            email: isGuest ? '' : (customer?.email || '')
          },
          theme: {
            color: orderData.theme_color || '#00a3ad'
          }
        };

        const rzp = new window.Razorpay(options);
        rzp.on('payment.failed', function (res) {
          alert('Payment failed: ' + res.error.description);
        });
        rzp.open();
      } else if (selectedGateway === 'stripe') {
        alert('Stripe modal initialized. Completing simulated payment...');
        await handleVerification(isGuest, amount, {
          gateway: 'stripe',
          stripe_payment_intent_id: 'mock_stripe_intent_' + Math.random().toString(36).substr(2, 9),
          mock: true,
        });
      } else {
        alert(`Completing simulated checkout with ${selectedGateway}...`);
        await handleVerification(isGuest, amount, {
          gateway: selectedGateway,
          paypal_order_id: orderData.order_id || 'mock_paypal_order_123',
          mock: true,
        });
      }

    } catch (err) {
      console.error(err);
      alert('An error occurred during payment init.');
    }
  };

  const handleVerification = async (isGuest, amount, verificationData) => {
    let bookingDetails = {};
    if (isGuest) {
      bookingDetails = buildCheckoutPayload({
        name: patientName,
        mobile: patientPhone,
        age: patientAge,
        gender: patientGender,
        address: 'Guest Checkout',
        booking_date: new Date().toISOString().split('T')[0],
        ...verificationData,
      });
    } else {
      const cust = getSuperlabCustomer();
      bookingDetails = buildCheckoutPayload({
        name: cust ? (cust.name || 'Logged-in Customer') : 'Online Customer',
        email: cust ? (cust.email || null) : null,
        mobile: cust ? cust.mobile : '',
        age: cust ? (cust.age || 30) : 30,
        gender: cust ? (cust.gender || 'Male') : 'Male',
        address: cust ? (cust.address || 'Profile Address') : 'Online Booking',
        booking_date: new Date().toISOString().split('T')[0],
        ...verificationData,
      });
    }

    const verifyRes = await verifyUnifiedPayment(bookingDetails);
    if (verifyRes && verifyRes.status === 'success') {
      setConfirmedTotal(amount);
      const key = window.getSuperlabCartKey ? window.getSuperlabCartKey() : 'superlab_cart_guest';
      localStorage.setItem(key, JSON.stringify([]));
      window.dispatchEvent(new Event('superlab_cart_update'));
      window.dispatchEvent(new Event('superlab_auth_update'));
      setCheckoutStep('success');
    } else {
      alert('Payment verification failed. Please contact support.');
    }
  };

  const handleGuestCheckoutSubmit = (e) => {
    e.preventDefault();
    handlePayment(true);
  };

  if (checkoutStep === 'success') {
    const displayPatientName = isLoggedIn ? (customer?.name || 'Customer') : (patientName || 'Patient');
    const displayAge = isLoggedIn ? (customer?.age || 'N/A') : (patientAge || 'N/A');
    const displayGender = isLoggedIn ? (customer?.gender || 'Male') : (patientGender || 'Male');
    const displayMobile = isLoggedIn ? (customer?.mobile || '') : patientPhone;

    return (
      <div style={{ backgroundColor: '#f8fafc', minHeight: '80vh', padding: '60px 20px', fontFamily: 'var(--sans)' }}>
        <div style={{
          maxWidth: '600px',
          margin: '0 auto',
          backgroundColor: '#ffffff',
          border: '1px solid var(--line)',
          borderRadius: '24px',
          padding: '40px',
          textAlign: 'center',
          boxShadow: 'var(--shadow-sm)'
        }}>
          <div style={{ display: 'flex', justifyContent: 'center', marginBottom: '24px' }}>
            <div style={{ backgroundColor: '#e8f5e9', color: '#2e7d32', width: '80px', height: '80px', borderRadius: '50%', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
              <CheckCircle size={44} />
            </div>
          </div>
          <h1 style={{ fontSize: '2rem', fontWeight: '800', color: 'var(--blue)', margin: '0 0 12px 0' }}>
            Order Placed Successfully!
          </h1>
          <p style={{ color: 'var(--muted)', fontSize: '1rem', lineHeight: '1.6', margin: '0 0 30px 0' }}>
            Thank you, <strong>{displayPatientName}</strong>. Your booking has been received. Our team will contact you shortly to confirm home sample collection.
          </p>
          <div style={{
            backgroundColor: 'var(--teal-soft)',
            borderRadius: '12px',
            padding: '20px',
            textAlign: 'left',
            marginBottom: '30px',
            border: '1px solid rgba(0, 163, 173, 0.1)'
          }}>
            <h3 style={{ margin: '0 0 10px 0', fontSize: '0.95rem', fontWeight: '800', color: 'var(--blue)' }}>Order Summary</h3>
            <div style={{ fontSize: '0.9rem', color: '#334155', display: 'flex', flexDirection: 'column', gap: '6px' }}>
              <div><strong>Patient:</strong> {displayPatientName} ({displayAge} Yrs, {displayGender})</div>
              {displayMobile && <div><strong>Mobile:</strong> {displayMobile}</div>}
              <div><strong>Amount:</strong> ₹ {confirmedTotal}</div>
            </div>
          </div>
          <a 
            href="#/" 
            style={{
              display: 'inline-block',
              backgroundColor: 'var(--blue)',
              color: '#ffffff',
              padding: '14px 40px',
              borderRadius: '10px',
              fontSize: '1rem',
              fontWeight: '700',
              textDecoration: 'none',
              transition: 'background-color 0.2s'
            }}
          >
            Go Back Home
          </a>
        </div>
      </div>
    );
  }

  return (
    <div className="cart-page-wrapper" style={{ backgroundColor: '#f8fafc', minHeight: '90vh', padding: '40px 20px', fontFamily: 'var(--sans)' }}>
      <style>{`
        .cart-grid {
          display: grid;
          grid-template-columns: 1fr 400px;
          gap: 30px;
          max-width: 1200px;
          margin: 0 auto;
          align-items: start;
        }
        .cart-card {
          background: #ffffff;
          border: 1px solid var(--line);
          border-radius: 20px;
          padding: 24px;
          box-shadow: var(--shadow-sm);
        }
        .item-row {
          display: flex;
          align-items: center;
          justify-content: space-between;
          padding: 20px 0;
          border-bottom: 1px solid var(--line);
        }
        .item-row:last-child {
          border-bottom: none;
        }
        .qty-btn {
          width: 32px;
          height: 32px;
          border-radius: 50%;
          border: 1px solid var(--line);
          background: #ffffff;
          display: flex;
          align-items: center;
          justify-content: center;
          cursor: pointer;
          color: var(--blue);
          transition: all 0.2s;
        }
        .qty-btn:hover {
          border-color: var(--teal);
          color: var(--teal);
        }
        .form-input {
          width: 100%;
          padding: 12px 14px;
          border: 1px solid var(--line);
          border-radius: 10px;
          font-size: 0.95rem;
          margin-top: 6px;
          outline: none;
          transition: border-color 0.2s;
        }
        .form-input:focus {
          border-color: var(--teal);
        }
        .checkout-btn {
          width: 100%;
          background: var(--blue);
          color: #ffffff;
          border: none;
          border-radius: 12px;
          padding: 14px 20px;
          font-size: 1.05rem;
          font-weight: 700;
          cursor: pointer;
          transition: background-color 0.2s;
          display: flex;
          align-items: center;
          justify-content: center;
          gap: 8px;
        }
        .checkout-btn:hover {
          background-color: #002c54;
        }
        @media (max-width: 968px) {
          .cart-grid {
            grid-template-columns: 1fr;
          }
        }
      `}</style>

      {/* Main Container */}
      <div className="cart-grid">
        
        {/* Left Column: Cart items & Checkout Steps */}
        <div style={{ display: 'flex', flexDirection: 'column', gap: '24px' }}>
          
          {checkoutStep === 'cart' ? (
            <div className="cart-card">
              <h2 style={{ fontSize: '1.4rem', fontWeight: '800', color: 'var(--blue)', marginTop: 0, marginBottom: '20px', textAlign: 'left' }}>
                Shopping Cart ({cartItems.length} items)
              </h2>

              {cartItems.length === 0 ? (
                <div style={{ textAlign: 'center', padding: '40px 0' }}>
                  <AlertCircle size={48} color="var(--muted)" style={{ marginBottom: '16px' }} />
                  <p style={{ color: 'var(--muted)', fontSize: '1.1rem', margin: '0 0 20px 0' }}>Your cart is empty.</p>
                  <a href="#/lab-tests" style={{ display: 'inline-block', backgroundColor: 'var(--teal)', color: 'white', padding: '12px 24px', borderRadius: '8px', fontWeight: '700', textDecoration: 'none' }}>
                    Browse Tests
                  </a>
                </div>
              ) : (
                <div>
                  {cartItems.map((item) => (
                    <div key={item.id} className="item-row">
                      <div style={{ textAlign: 'left', flex: 1, paddingRight: '16px' }}>
                        <span style={{ fontSize: '0.8rem', color: 'var(--teal)', fontWeight: '700', textTransform: 'uppercase' }}>
                          {item.category}
                        </span>
                        <h4 style={{ fontSize: '1.05rem', fontWeight: '800', color: 'var(--blue)', margin: '4px 0 0 0' }}>
                          {item.name}
                        </h4>
                      </div>
                      
                      {/* Pricing & Trash */}
                      <div style={{ display: 'flex', alignItems: 'center', gap: '24px' }}>
                        <div style={{ textAlign: 'right' }}>
                          <span style={{ fontSize: '1.15rem', fontWeight: '800', color: 'var(--teal)' }}>
                            ₹ {linePrice(item)}
                          </span>
                          {lineOriginal(item) && lineOriginal(item) > linePrice(item) && (
                            <span style={{ display: 'block', fontSize: '0.85rem', color: 'var(--muted)', textDecoration: 'line-through' }}>
                              ₹ {lineOriginal(item)}
                            </span>
                          )}
                        </div>
                        <button 
                          onClick={() => handleRemoveItem(item.id)}
                          style={{ background: 'none', border: 'none', color: '#ef4444', cursor: 'pointer', padding: '4px', display: 'flex', alignItems: 'center' }}
                          title="Remove item"
                        >
                          <Trash2 size={18} />
                        </button>
                      </div>
                    </div>
                  ))}

                  {/* Proceed to checkout button */}
                  <div style={{ display: 'flex', justifyContent: 'flex-end', gap: '16px', marginTop: '30px', flexWrap: 'wrap' }}>
                    {isLoggedIn ? (
                      <>
                        <button 
                          className="checkout-btn" 
                          style={{ maxWidth: '240px', backgroundColor: 'var(--teal)' }}
                          onClick={() => handleBNPLCheckout(false)}
                        >
                          Book Now Pay Later
                        </button>
                        <button 
                          className="checkout-btn" 
                          style={{ maxWidth: '260px' }}
                          onClick={() => handlePayment(false)}
                        >
                          Proceed to Make Payment
                        </button>
                      </>
                    ) : (
                      <button className="checkout-btn" style={{ maxWidth: '300px' }} onClick={() => setCheckoutStep('checkout')}>
                        Proceed to Patient Details
                      </button>
                    )}
                  </div>
                </div>
              )}
            </div>
          ) : (
            <div className="cart-card">
              {/* Back Button */}
              <button 
                onClick={() => setCheckoutStep('cart')}
                style={{ display: 'inline-flex', alignItems: 'center', gap: '6px', background: 'none', border: 'none', color: 'var(--teal)', fontWeight: '700', cursor: 'pointer', marginBottom: '20px', padding: 0 }}
              >
                <ArrowLeft size={16} /> Back to Cart
              </button>

              <h2 style={{ fontSize: '1.4rem', fontWeight: '800', color: 'var(--blue)', marginTop: 0, marginBottom: '24px', textAlign: 'left' }}>
                Patient & Appointment Details
              </h2>

              <form onSubmit={handleGuestCheckoutSubmit} style={{ display: 'flex', flexDirection: 'column', gap: '20px', textAlign: 'left' }}>
                
                {/* Patient details section */}
                <div>
                  <h4 style={{ margin: '0 0 12px 0', fontSize: '1rem', fontWeight: '800', color: 'var(--blue)' }}>
                    Patient Info
                  </h4>
                  <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: '16px' }}>
                    <div>
                      <label style={{ fontSize: '0.85rem', fontWeight: '700', color: 'var(--muted)' }}>Patient Name</label>
                      <input 
                        type="text" 
                        required 
                        className="form-input" 
                        placeholder="Enter full name" 
                        value={patientName}
                        onChange={(e) => setPatientName(e.target.value)}
                      />
                    </div>
                    <div>
                      <label style={{ fontSize: '0.85rem', fontWeight: '700', color: 'var(--muted)' }}>Mobile Number</label>
                      <input 
                        type="tel" 
                        required 
                        maxLength="10"
                        className="form-input" 
                        placeholder="10-digit mobile" 
                        value={patientPhone}
                        onChange={(e) => setPatientPhone(e.target.value)}
                      />
                    </div>
                    <div>
                      <label style={{ fontSize: '0.85rem', fontWeight: '700', color: 'var(--muted)' }}>Age (Years)</label>
                      <input 
                        type="number" 
                        required 
                        className="form-input" 
                        placeholder="Enter age" 
                        value={patientAge}
                        onChange={(e) => setPatientAge(e.target.value)}
                      />
                    </div>
                  </div>
                  
                  <div style={{ marginTop: '14px' }}>
                    <span style={{ fontSize: '0.85rem', fontWeight: '700', color: 'var(--muted)', display: 'block', marginBottom: '6px' }}>Gender</span>
                    <div style={{ display: 'flex', gap: '14px' }}>
                      {['Male', 'Female', 'Other'].map(g => (
                        <label key={g} style={{ display: 'flex', alignItems: 'center', gap: '6px', fontSize: '0.95rem', cursor: 'pointer' }}>
                          <input 
                            type="radio" 
                            name="gender" 
                            checked={patientGender === g}
                            onChange={() => setPatientGender(g)}
                          />
                          <span>{g}</span>
                        </label>
                      ))}
                    </div>
                  </div>
                </div>

                <div style={{ display: 'flex', gap: '16px', marginTop: '20px' }}>
                  <button 
                    type="button" 
                    className="checkout-btn" 
                    style={{ backgroundColor: 'var(--teal)', flex: 1 }}
                    onClick={() => handleBNPLCheckout(true)}
                  >
                    Book Now Pay Later
                  </button>
                  <button type="submit" className="checkout-btn" style={{ flex: 1 }}>
                    <ShieldCheck size={20} /> Proceed to Make Payment
                  </button>
                </div>
              </form>
            </div>
          )}
        </div>

        {/* Right Column: Bill Details Summary Card */}
        <div style={{ display: 'flex', flexDirection: 'column', gap: '24px' }}>
          <div className="cart-card" style={{ textAlign: 'left' }}>
            <h3 style={{ fontSize: '1.15rem', fontWeight: '800', color: 'var(--blue)', margin: '0 0 20px 0' }}>
              Payment details
            </h3>

            {/* Coupons box — hidden when a custom package is in the cart (MYOP discount already applied) */}
            {!couponsDisabled && (
            <div style={{ marginBottom: '24px' }}>
              <label style={{ fontSize: '0.82rem', fontWeight: '700', color: 'var(--muted)', display: 'block', marginBottom: '6px' }}>
                APPLY PROMO CODE
              </label>
              <div style={{ display: 'flex', gap: '8px' }}>
                <input 
                  type="text" 
                  className="form-input" 
                  style={{ margin: 0, textTransform: 'uppercase' }} 
                  value={couponCode}
                  placeholder="Enter Coupon"
                  onChange={(e) => setCouponCode(e.target.value)}
                />
                <button 
                  onClick={handleApplyCoupon}
                  disabled={couponLoading}
                  style={{
                    backgroundColor: 'var(--teal)',
                    color: '#ffffff',
                    border: 'none',
                    borderRadius: '10px',
                    padding: '0 20px',
                    fontSize: '0.9rem',
                    fontWeight: '700',
                    cursor: couponLoading ? 'wait' : 'pointer',
                    opacity: couponLoading ? 0.7 : 1,
                  }}
                >
                  {couponLoading ? 'Checking...' : 'Apply'}
                </button>
              </div>
              {couponError && (
                <div style={{ color: '#dc2626', fontSize: '0.82rem', fontWeight: '600', marginTop: '6px' }}>{couponError}</div>
              )}
              {appliedCoupon && (
                <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', color: '#2e7d32', fontSize: '0.82rem', fontWeight: '700', marginTop: '6px' }}>
                  <span>✓ Code {appliedCoupon.code} applied ({appliedCoupon.label})</span>
                  <button 
                    onClick={handleRemoveCoupon}
                    style={{ background: 'none', border: 'none', color: '#ef4444', textDecoration: 'underline', cursor: 'pointer', fontWeight: '700', padding: 0 }}
                  >
                    Remove
                  </button>
                </div>
              )}
              {availableCoupons.length > 0 && (
                <div style={{ marginTop: '14px' }}>
                  <div style={{ fontSize: '0.78rem', fontWeight: '700', color: 'var(--muted)', marginBottom: '4px', textTransform: 'uppercase' }}>
                    {isLoggedIn && availableCoupons.some((c) => c.is_assigned)
                      ? 'Your available offers'
                      : 'Available offers'}
                  </div>
                  <p style={{ fontSize: '0.76rem', color: '#94a3b8', margin: '0 0 8px 0' }}>
                    Only one coupon can be used per order.
                  </p>
                  <div style={{ display: 'flex', flexDirection: 'column', gap: '8px' }}>
                    {availableCoupons.map((offer) => {
                      const isApplied = appliedCoupon?.code === offer.code;
                      const hasOtherApplied = appliedCoupon && !isApplied;
                      const minOrderNote = offer.min_order_amount
                        ? `Min order ₹${Number(offer.min_order_amount).toLocaleString('en-IN')}`
                        : null;
                      const actionLabel = isApplied ? 'Applied' : (hasOtherApplied ? 'Switch' : 'Apply');

                      return (
                        <button
                          key={offer.code}
                          type="button"
                          disabled={couponLoading || isApplied}
                          onClick={() => handleApplyCoupon(offer.code)}
                          style={{
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'space-between',
                            gap: '12px',
                            width: '100%',
                            textAlign: 'left',
                            border: isApplied ? '1.5px solid #2e7d32' : '1px dashed #cbd5e1',
                            borderRadius: '10px',
                            padding: '10px 12px',
                            background: isApplied ? '#f0fdf4' : '#f8fafc',
                            cursor: couponLoading || isApplied ? 'default' : 'pointer',
                          }}
                        >
                          <div>
                            <div style={{ fontWeight: '800', color: 'var(--blue)', letterSpacing: '0.04em' }}>{offer.code}</div>
                            <div style={{ fontSize: '0.8rem', color: '#64748b', marginTop: '2px' }}>
                              {offer.label}
                              {offer.name ? ` · ${offer.name}` : ''}
                              {minOrderNote ? ` · ${minOrderNote}` : ''}
                            </div>
                          </div>
                          <span style={{ fontSize: '0.78rem', fontWeight: '700', color: isApplied ? '#2e7d32' : 'var(--teal)', whiteSpace: 'nowrap' }}>
                            {actionLabel}
                          </span>
                        </button>
                      );
                    })}
                  </div>
                </div>
              )}
              {availableCouponsLoading && availableCoupons.length === 0 && (
                <div style={{ fontSize: '0.8rem', color: 'var(--muted)', marginTop: '10px' }}>Loading offers...</div>
              )}
            </div>
            )}

            <div style={{ display: 'flex', flexDirection: 'column', gap: '12px', borderTop: '1px solid var(--line)', paddingTop: '16px' }}>
              
              <div style={{ display: 'flex', justifyContent: 'space-between', fontSize: '0.92rem', color: '#64748b' }}>
                <span>Items subtotal</span>
                <span>₹ {getItemsSubtotal()}</span>
              </div>
              
              {getCatalogSavings() > 0 && (
              <div style={{ display: 'flex', justifyContent: 'space-between', fontSize: '0.92rem', color: '#2e7d32', fontWeight: '600' }}>
                <span>Catalog savings</span>
                <span>- ₹ {getCatalogSavings()}</span>
              </div>
              )}

              {getCouponDiscount() > 0 && (
              <div style={{ display: 'flex', justifyContent: 'space-between', fontSize: '0.92rem', color: '#2e7d32', fontWeight: '600' }}>
                <span>Coupon discount ({appliedCoupon?.code})</span>
                <span>- ₹ {getCouponDiscount()}</span>
              </div>
              )}

              <div style={{ display: 'flex', justifyContent: 'space-between', fontSize: '0.92rem', color: '#64748b' }}>
                <span>Home Collection Charges</span>
                <span style={{ color: '#2e7d32', fontWeight: '600' }}>FREE</span>
              </div>

              <hr style={{ border: 'none', borderTop: '1px solid var(--line)', margin: '8px 0' }} />

              <div style={{ display: 'flex', justifyContent: 'space-between', fontSize: '1.25rem', fontWeight: '800', color: 'var(--blue)' }}>
                <span>Total Amount</span>
                <span style={{ color: 'var(--teal)' }}>₹ {getGrandTotal()}</span>
              </div>
            </div>

            {/* Payment Gateway Selector - only shown if multiple options are active in CRM */}
            {gateways.length > 1 && (
              <div style={{ marginTop: '24px', borderTop: '1px solid var(--line)', paddingTop: '20px' }}>
                <label style={{ fontSize: '0.82rem', fontWeight: '700', color: 'var(--muted)', display: 'block', marginBottom: '12px', textTransform: 'uppercase' }}>
                  Select Payment Gateway
                </label>
                <div style={{ display: 'flex', flexDirection: 'column', gap: '10px' }}>
                  {gateways.map((gw) => {
                    const isSelected = selectedGateway === gw.slug;
                    return (
                      <label
                        key={gw.slug}
                        style={{
                          display: 'flex',
                          alignItems: 'center',
                          gap: '12px',
                          border: isSelected ? '2px solid var(--teal)' : '1px solid var(--line)',
                          borderRadius: '12px',
                          padding: '12px 16px',
                          cursor: 'pointer',
                          background: isSelected ? 'var(--teal-soft)' : '#ffffff',
                          transition: 'all 0.2s',
                        }}
                      >
                        <input
                          type="radio"
                          name="payment_gateway"
                          checked={isSelected}
                          onChange={() => setSelectedGateway(gw.slug)}
                          style={{ accentColor: 'var(--teal)' }}
                        />
                        <div style={{ flex: 1 }}>
                          <div style={{ fontWeight: '800', color: 'var(--blue)', fontSize: '0.95rem' }}>{gw.name}</div>
                          {gw.slug === 'razorpay' && <div style={{ fontSize: '0.78rem', color: 'var(--muted)' }}>Cards, Netbanking, UPI</div>}
                          {gw.slug === 'stripe' && <div style={{ fontSize: '0.78rem', color: 'var(--muted)' }}>International Cards</div>}
                          {gw.slug === 'paypal' && <div style={{ fontSize: '0.78rem', color: 'var(--muted)' }}>PayPal Account, Venmo</div>}
                        </div>
                      </label>
                    );
                  })}
                </div>
              </div>
            )}

            {/* Inclusions trust points */}
            <div style={{
              backgroundColor: 'var(--teal-soft)',
              borderRadius: '12px',
              padding: '16px',
              display: 'flex',
              flexDirection: 'column',
              gap: '10px',
              marginTop: '24px'
            }}>
              <div style={{ display: 'flex', alignItems: 'center', gap: '8px', fontSize: '0.85rem', color: '#00808a', fontWeight: '700' }}>
                <ShieldCheck size={16} />
                <span>NABL Accredited Lab Partners</span>
              </div>
              <div style={{ display: 'flex', alignItems: 'center', gap: '8px', fontSize: '0.85rem', color: '#00808a', fontWeight: '700' }}>
                <CheckCircle size={16} />
                <span>Free Doctor Consult Included</span>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  );
};

export default CartPage;
