import { createBrowserRouter, Navigate } from "react-router";
import { Layout } from "./components/Layout";
import { AdminLayout } from "./components/AdminLayout";
import { HomePage } from "./components/customer/HomePage";
import { MenuPage } from "./components/customer/MenuPage";
import { CartPage } from "./components/customer/CartPage";
import { ProfilePage } from "./components/customer/ProfilePage";
import { CreateGroupOrder, GroupOrderRoom } from "./components/customer/GroupOrderRoom";
import { SplitBill } from "./components/customer/SplitBill";
import { KDSPage } from "./components/admin/KDSPage";
import { SmartPrepPage } from "./components/admin/SmartPrepPage";
import { DispatchPage } from "./components/admin/DispatchPage";
import { BranchDashboard } from "./components/admin/BranchDashboard";
import { SuperAdminPage } from "./components/admin/SuperAdminPage";
import { CartProvider } from "./context/CartContext";

function ClientWrapper() {
  return (
    <CartProvider>
      <Layout />
    </CartProvider>
  );
}

export const router = createBrowserRouter([
  {
    path: "/",
    Component: ClientWrapper,
    children: [
      { index: true, Component: HomePage },
      { path: "menu", Component: MenuPage },
      { path: "cart", Component: CartPage },
      { path: "profile", Component: ProfilePage },
      { path: "group-order", Component: CreateGroupOrder },
      { path: "group-order/:roomId", Component: GroupOrderRoom },
      { path: "split-bill/:roomId", Component: SplitBill },
    ],
  },
  {
    path: "/admin",
    Component: AdminLayout,
    children: [
      { index: true, element: <Navigate to="/admin/kds" replace /> },
      { path: "kds", Component: KDSPage },
      { path: "smart-prep", Component: SmartPrepPage },
      { path: "dispatch", Component: DispatchPage },
      { path: "branch", Component: BranchDashboard },
      { path: "super", Component: SuperAdminPage },
    ],
  },
]);

