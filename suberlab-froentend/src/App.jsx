


import { useState, useEffect } from 'react';
import Header from './components/Header';
import HeroSlider from './components/HeroSlider';
import QuickActions from './components/QuickActions';
import CategorySlider from './components/CategorySlider';
import ContactBanners from './components/ContactBanners';
import VitalOrgansSlider from './components/VitalOrgansSlider';
import LifestyleDiseaseSlider from './components/LifestyleDiseaseSlider';
import RequestCallbackBanner from './components/RequestCallbackBanner';
import FeaturedCheckups from './components/FeaturedCheckups';
import CustomPackageBanner from './components/CustomPackageBanner';
import TestSliders from './components/TestSliders';
import WhyChooseUs from './components/WhyChooseUs';
import HomepageReviews from './components/HomepageReviews';
import ExpandingFootprints from './components/ExpandingFootprints';
import Footer from './components/Footer';
import DynamicTestPage from './components/DynamicTestPage';
import MakeYourOwnPackagePage from './components/MakeYourOwnPackagePage';
import LabTestsPage from './components/LabTestsPage';
import DynamicPackagePage from './components/DynamicPackagePage';
import CartPage from './components/CartPage';
import ProfilePage from './components/ProfilePage';
import ReportDownloadPage from './components/ReportDownloadPage';
import LegalPage from './components/LegalPage';
import { normalizeCartItem } from './utils/pricing';

// Initialize global cart helper functions and localStorage setup
if (typeof window !== 'undefined') {
  window.getSuperlabCartKey = () => {
    try {
      const customer = localStorage.getItem('superlab_customer');
      if (customer) {
        const parsed = JSON.parse(customer);
        if (parsed && parsed.id) {
          return `superlab_cart_user_${parsed.id}`;
        }
      }
    } catch (e) { }
    return 'superlab_cart_guest';
  };

  window.getSuperlabCart = () => {
    try {
      const key = window.getSuperlabCartKey();
      return JSON.parse(localStorage.getItem(key) || '[]');
    } catch {
      return [];
    }
  };

  window.showSuperlabToast = (message) => {
    const oldToast = document.querySelector('.superlab-toast');
    if (oldToast) {
      oldToast.remove();
    }
    const toast = document.createElement('div');
    toast.className = 'superlab-toast';
    toast.innerHTML = `
      <div class="superlab-toast-success-icon">✓</div>
      <span>${message}</span>
    `;
    document.body.appendChild(toast);
    toast.offsetHeight;
    toast.classList.add('show');
    setTimeout(() => {
      toast.classList.remove('show');
      setTimeout(() => toast.remove(), 300);
    }, 3000);
  };

  window.normalizeSuperlabCartItem = (item) => normalizeCartItem(item);

  window.addToSuperlabCart = (item) => {
    const normalized = window.normalizeSuperlabCartItem(item);
    const key = window.getSuperlabCartKey();
    const cart = window.getSuperlabCart();
    const exists = cart.find(i => i.id === normalized.id || i.name === normalized.name);
    if (!exists) {
      cart.push(normalized);
      localStorage.setItem(key, JSON.stringify(cart));
      window.dispatchEvent(new Event('superlab_cart_update'));
      window.showSuperlabToast(`${normalized.name} added to cart successfully!`);
    } else {
      window.showSuperlabToast(`${normalized.name} is already in your cart.`);
    }
  };

  window.removeFromSuperlabCart = (item) => {
    const key = window.getSuperlabCartKey();
    const cart = window.getSuperlabCart();
    const updated = cart.filter(i => {
      const idMatch = (i.id != null && item.id != null && String(i.id).trim() === String(item.id).trim());
      const nameMatch = (i.name && item.name && i.name.toLowerCase().trim() === item.name.toLowerCase().trim());
      return !(idMatch || nameMatch);
    });
    localStorage.setItem(key, JSON.stringify(updated));
    window.dispatchEvent(new Event('superlab_cart_update'));
    window.showSuperlabToast(`${item.name} removed from cart.`);
  };

  window.addEventListener('superlab_auth_update', () => {
    const guestKey = 'superlab_cart_guest';
    const activeKey = window.getSuperlabCartKey();
    if (activeKey !== guestKey) {
      try {
        const guestCart = JSON.parse(localStorage.getItem(guestKey) || '[]');
        if (guestCart.length > 0) {
          const userCart = JSON.parse(localStorage.getItem(activeKey) || '[]');
          const merged = [...userCart];
          guestCart.forEach(gItem => {
            const normalized = window.normalizeSuperlabCartItem ? window.normalizeSuperlabCartItem(gItem) : gItem;
            const exists = merged.some(uItem => uItem.id === normalized.id || uItem.name === normalized.name);
            if (!exists) {
              merged.push(normalized);
            }
          });
          localStorage.setItem(activeKey, JSON.stringify(merged));
          localStorage.setItem(guestKey, JSON.stringify([]));
        }
      } catch (e) {
        console.error('Error merging carts:', e);
      }
    }
    window.dispatchEvent(new Event('superlab_cart_update'));
  });
}

const App = () => {
  const [currentHash, setCurrentHash] = useState(window.location.hash);
  const [isIsoModalOpen, setIsIsoModalOpen] = useState(false);

  useEffect(() => {
    window.dispatchEvent(new Event('superlab_cart_update'));

    const handleHashChange = () => {
      setCurrentHash(window.location.hash);
      window.scrollTo(0, 0);
    };
    window.addEventListener('hashchange', handleHashChange);
    return () => window.removeEventListener('hashchange', handleHashChange);
  }, []);

  useEffect(() => {
    if (currentHash.startsWith('#/all-categories') || currentHash.startsWith('#all-categories')) {
      window.location.hash = '#/lab-tests';
    }
  }, [currentHash]);

  useEffect(() => {
    if (isIsoModalOpen) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = '';
    }
    return () => {
      document.body.style.overflow = '';
    };
  }, [isIsoModalOpen]);

  const renderContent = () => {
    if (currentHash === '#/privacy' || currentHash === '#privacy') {
      return <LegalPage page="privacy" />;
    }
    if (currentHash === '#/terms' || currentHash === '#terms') {
      return <LegalPage page="terms" />;
    }
    if (currentHash === '#/download-report') {
      return <ReportDownloadPage />;
    }
    if (currentHash.startsWith('#/test/')) {
      const slug = currentHash.replace('#/test/', '');
      return <DynamicTestPage testSlug={slug} setIsIsoModalOpen={setIsIsoModalOpen} />;
    }
    if (currentHash === '#/make-package') {
      return <MakeYourOwnPackagePage />;
    }
    if (currentHash === '#/lab-tests') {
      return <LabTestsPage />;
    }
    if (currentHash === '#/cart' || currentHash === '#cart') {
      return <CartPage />;
    }
    if (currentHash.startsWith('#/package/')) {
      const slug = currentHash.replace('#/package/', '');
      return <DynamicPackagePage packageSlug={slug} setIsIsoModalOpen={setIsIsoModalOpen} />;
    }
    if (currentHash === '#/profile') {
      return <ProfilePage />;
    }

    return (
      <>
        <HeroSlider />
        <QuickActions />
        <CategorySlider />
        <FeaturedCheckups />
        <CustomPackageBanner />
        <TestSliders />
        <ContactBanners />
        <VitalOrgansSlider />
        <LifestyleDiseaseSlider />
        <RequestCallbackBanner />
        <WhyChooseUs />
        <HomepageReviews />
        {/* <ExpandingFootprints /> */}
      </>
    );
  };

  return (
    <div className="app-container">
      <Header isIsoModalOpen={isIsoModalOpen} setIsIsoModalOpen={setIsIsoModalOpen} />
      {renderContent()}
      <Footer isIsoModalOpen={isIsoModalOpen} />
    </div>
  );
};

export default App;
