import React, { useState } from "react";
import { Outlet, useLocation } from "react-router-dom";
import { useAuth } from "@/_shared/context/AuthContext";
import { Alert, Drawer } from "@/_shared/components/ui";
import { 
  LayoutDashboard, 
  Monitor, 
  Zap, 
  CreditCard, 
  Key, 
  Grid, 
  Bolt, 
  Layers, 
  FileText, 
  Settings,
  Users,
  TimerReset
} from "lucide-react";

// Layout Components
import { AdminSidebar } from "./AdminSidebar";
import { AdminHeader } from "./AdminHeader";

// Styles
import "@/_shared/components/core/DashboardLayout.css";


//  CẤU HÌNH MENU ĐIỀU HƯỚNG (ADMIN)
const ADMIN_NAV = [
  {
    group: "Chung",
    items: [
      { to: "/admin",            label: "Tổng quan",       icon: LayoutDashboard, end: true },
      { to: "/admin/usage",      label: "Sử dụng",          icon: Monitor },
      { to: "/admin/revenue",    label: "Doanh thu",        icon: Zap },
    ]
  },
  {
    group: "Sản phẩm & Giấy phép",
    items: [
      { to: "/admin/packages",   label: "Gói dịch vụ",      icon: CreditCard },
      { to: "/admin/licenses",   label: "Giấy phép",       icon: Key },
      { to: "/admin/releases",   label: "Phiên bản Plugin", icon: Grid },
    ]
  },
  {
    group: "Hạ tầng",
    items: [
      { to: "/admin/providers",  label: "Nhà cung cấp AI",  icon: Bolt },
      { to: "/admin/keys",       label: "Kho khóa API",    icon: Layers },
    ]
  },
  {
    group: "Hệ thống",
    items: [
      { to: "/admin/users",      label: "Người dùng",      icon: Users },
      { to: "/admin/cron",       label: "Tác vụ tự động",    icon: TimerReset },
      { to: "/admin/audit-log",  label: "Nhật ký hệ thống", icon: FileText },
      { to: "/admin/settings",   label: "Cài đặt",         icon: Settings },
    ]
  }
];

/**
 * AdminLayout: Khung giao diện chính của hệ thống quản trị.
 */
export function AdminLayout() {
  const { MOCK_MODE } = useAuth();
  const location = useLocation();
  const [mobileOpen, setMobileOpen] = useState(false);

  // Lấy tiêu đề trang hiện tại dựa trên đường dẫn
  const currentTitle = ADMIN_NAV.flatMap(g => g.items)
    .find(i => i.to === location.pathname || (location.pathname.startsWith(i.to) && i.to !== "/admin"))
    ?.label || "Tổng quan";

  return (
    <div className="dash" data-variant="admin">
      {/* Sidebar - Bản Desktop */}
      <aside className="dash__sidebar">
        <AdminSidebar nav={ADMIN_NAV} />
      </aside>

      {/* Sidebar - Bản Mobile (Drawer) */}
      <Drawer open={mobileOpen} onClose={() => setMobileOpen(false)} side="left" title="Menu">
        <div className="h-full bg-base-300">
          <AdminSidebar nav={ADMIN_NAV} onLinkClick={() => setMobileOpen(false)} />
        </div>
      </Drawer>

      <main className="dash__main custom-scrollbar">
        {/* Header hệ thống */}
        <AdminHeader title={currentTitle} onMenuClick={() => setMobileOpen(true)} />

        {/* Master Container cho toàn bộ nội dung Admin */}
        <div className="mx-auto w-full max-w-[1600px] px-6 lg:px-10 flex flex-col flex-1">
          {/* Cảnh báo hệ thống Mock */}
          {MOCK_MODE && (
            <div className="mt-6">
              <Alert tone="warning" className="rounded-2xl border-warning/20 bg-warning/5 backdrop-blur-md">
                Hệ thống Backend đang ngoại tuyến. Dữ liệu hiện tại là giả lập (Mock Data).
              </Alert>
            </div>
          )}

          {/* Nội dung chi tiết các trang */}
          <div className="py-8 lg:py-10 flex flex-col flex-1">
            <Outlet />
          </div>
        </div>
      </main>
    </div>
  );
}

export default AdminLayout;
