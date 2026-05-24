import { lazy, Suspense } from 'react';
import { Routes, Route, Navigate } from 'react-router-dom';
import { Toaster } from 'sonner';

// Public pages
const HomePage = lazy(() => import('./features/home/HomePage'));
const Catalog = lazy(() => import('./features/catalog/Catalog'));
const ProductEcosystemPage = lazy(() => import('./features/public-misc/ProductEcosystemPage'));
const PricingPage = lazy(() => import('./features/pricing/PricingPage'));
const AboutPage = lazy(() => import('./features/public-misc/AboutPage'));
const FaqPage = lazy(() => import('./features/public-misc/FaqPage'));
const DocsPage = lazy(() => import('./features/docs/DocsPage'));
const ContactPage = lazy(() => import('./features/public-misc/ContactPage'));
const NotFoundPage = lazy(() => import('./features/public-misc/NotFoundPage'));

// Auth pages
const LoginPage = lazy(() => import('./features/auth/LoginPage'));
const SignupPage = lazy(() => import('./features/auth/SignupPage'));
const ForgotPasswordPage = lazy(() => import('./features/auth/ForgotPasswordPage'));

// User layout & overview
const UserLayout = lazy(() => import('@/features/user/UserLayout'));
const UserOverviewPage = lazy(() => import('@/features/user/OverviewPage'));

// Relocated User Pages
const BillingPage = lazy(() => import('@/features/billing/BillingPage'));
const CheckoutSuccessPage = lazy(() => import('@/features/checkout/CheckoutSuccessPage'));
const ApiKeysPage = lazy(() => import('@/features/user/ApiKeysPage'));
const UserLicensesPage = lazy(() => import('@/features/license/LicensePage'));
const DownloadsPage = lazy(() => import('@/features/user/DownloadsPage'));
const ProfilePage = lazy(() => import('@/features/user/ProfilePage'));
const SupportPage = lazy(() => import('@/features/support/SupportPage'));
const WalletPage = lazy(() => import('@/features/wallet/WalletPage'));
const LedgerPage = lazy(() => import('@/features/ledger/LedgerPage'));
const UserUsagePage = lazy(() => import('@/features/user/UsagePage'));

// Admin pages
const AdminLayout = lazy(() => import('@admin/layout/AdminLayout'));
const OverviewPage = lazy(() => import('@admin/features/overview'));
const ProvidersPage = lazy(() => import('@admin/features/providers'));
const ProviderEditPage = lazy(() => import('@admin/features/providers/ProviderEditPage'));
const UsagePage = lazy(() => import('@admin/features/usage'));
const PackagesPage = lazy(() => import('@admin/features/packages'));
const PackageEditPage = lazy(() => import('@admin/features/packages/PackageEditPage'));
const RevenuePage = lazy(() => import('@admin/features/revenue'));
const LicensesPage = lazy(() => import('@admin/features/licenses'));
const LicenseCreatePage = lazy(() => import('@admin/features/licenses/LicenseCreatePage'));
const LicenseDetailPage = lazy(() => import('@admin/features/licenses/LicenseDetailPage'));
const LicenseAssignPackagePage = lazy(() => import('@admin/features/licenses/LicenseAssignPackagePage'));
const LicenseAdjustTokensPage = lazy(() => import('@admin/features/licenses/LicenseAdjustTokensPage'));
const KeysPage = lazy(() => import('@admin/features/keys'));
const KeyCreatePage = lazy(() => import('@admin/features/keys/KeyCreatePage'));
const KeyAllocatePage = lazy(() => import('@admin/features/keys/KeyAllocatePage'));
const KeyBulkImportPage = lazy(() => import('@admin/features/keys/KeyBulkImportPage'));
const AuditLogPage = lazy(() => import('@admin/features/audit'));
const SettingsPage = lazy(() => import('@admin/features/settings'));
const ReleasesPage = lazy(() => import('@admin/features/releases'));
const ReleaseUploadPage = lazy(() => import('@admin/features/releases/ReleaseUploadPage'));
const UsersPage = lazy(() => import('@admin/features/users').then(m => ({ default: m.UsersPage })));
const UserProfilePage = lazy(() => import('@admin/features/users').then(m => ({ default: m.UserProfilePage })));
const CronPage = lazy(() => import('@admin/features/cron'));

// Public layout (non-lazy for shell stability)
import PublicLayout from './features/public-misc/PublicLayout';
import { FullPageLoader } from './_shared/components/ui/FullPageLoader';

function App() {
  return (
    <>
      <Toaster position="top-right" richColors />
      <Suspense fallback={<FullPageLoader />}>
        <Routes>
          {/* Public Routes */}
          <Route element={<PublicLayout />}>
            <Route path="/" element={<HomePage />} />
            <Route path="/catalog" element={<Catalog />} />
            <Route path="/catalog/:slug" element={<ProductEcosystemPage />} />
            <Route path="/pricing" element={<PricingPage />} />
            <Route path="/about" element={<AboutPage />} />
            <Route path="/faq" element={<FaqPage />} />
            <Route path="/docs" element={<DocsPage />} />
            <Route path="/contact" element={<ContactPage />} />
          </Route>

          {/* Auth Routes */}
          <Route path="/auth/login" element={<LoginPage />} />
          <Route path="/auth/signup" element={<SignupPage />} />
          <Route path="/auth/forgot-password" element={<ForgotPasswordPage />} />
          
          {/* Redirect legacy routes */}
          <Route path="/login" element={<Navigate to="/auth/login" replace />} />
          <Route path="/signup" element={<Navigate to="/auth/signup" replace />} />
          <Route path="/forgot-password" element={<Navigate to="/auth/forgot-password" replace />} />

          {/* User Routes */}
          <Route path="/app" element={<UserLayout />}>
            <Route index element={<UserOverviewPage />} />
            <Route path="billing" element={<BillingPage />} />
            <Route path="checkout-success" element={<CheckoutSuccessPage />} />
            <Route path="api-keys" element={<ApiKeysPage />} />
            <Route path="licenses" element={<UserLicensesPage />} />
            <Route path="downloads" element={<DownloadsPage />} />
            <Route path="profile" element={<ProfilePage />} />
            <Route path="support" element={<SupportPage />} />
            <Route path="wallet" element={<WalletPage />} />
            <Route path="ledger" element={<LedgerPage />} />
            <Route path="usage" element={<UserUsagePage />} />
          </Route>

          {/* Admin Routes */}
          <Route path="/admin" element={<AdminLayout />}>
            <Route index element={<OverviewPage />} />
            <Route path="usage" element={<UsagePage />} />
            <Route path="revenue" element={<RevenuePage />} />
            <Route path="packages" element={<PackagesPage />} />
            <Route path="packages/new" element={<PackageEditPage />} />
            <Route path="packages/:slug/edit" element={<PackageEditPage />} />
            <Route path="licenses" element={<LicensesPage />} />
            <Route path="licenses/new" element={<LicenseCreatePage />} />
            <Route path="licenses/:id" element={<LicenseDetailPage />} />
            <Route path="licenses/:id/assign-package" element={<LicenseAssignPackagePage />} />
            <Route path="licenses/:id/adjust-tokens" element={<LicenseAdjustTokensPage />} />
            <Route path="releases" element={<ReleasesPage />} />
            <Route path="releases/new" element={<ReleaseUploadPage />} />
            <Route path="keys" element={<KeysPage />} />

            <Route path="keys/new" element={<KeyCreatePage />} />
            <Route path="keys/:id/allocate" element={<KeyAllocatePage />} />
            <Route path="keys/bulk-import" element={<KeyBulkImportPage />} />
            <Route path="audit-log" element={<AuditLogPage />} />
            <Route path="settings" element={<SettingsPage />} />
            <Route path="users" element={<UsersPage />} />
            <Route path="users/:id" element={<UserProfilePage />} />
            <Route path="cron" element={<CronPage />} />
          </Route>

          {/* Fallback */}
          <Route path="*" element={<NotFoundPage />} />
        </Routes>
      </Suspense>
    </>
  );
}

export default App;
