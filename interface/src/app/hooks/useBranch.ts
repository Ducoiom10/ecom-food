import { useState, useCallback, useMemo } from "react";
import { calculateDistance } from "../utils/distance";

export interface Branch {
  id: string;
  name: string;
  address: string;
  status: "open" | "closed";
  lat: number;
  lng: number;
}

// Vị trí mặc định của user (Q1, TP.HCM) - trong thực tế sẽ lấy từ GPS
const DEFAULT_USER_LOCATION = { lat: 10.7769, lng: 106.7009 };

export const BRANCHES: Branch[] = [
  {
    id: "b1",
    name: "Chi nhánh Quận 1",
    address: "123 Nguyễn Huệ, Q1",
    status: "open",
    lat: 10.776,
    lng: 106.701,
  },
  {
    id: "b2",
    name: "Chi nhánh Quận 3",
    address: "45 Võ Văn Tần, Q3",
    status: "open",
    lat: 10.78,
    lng: 106.688,
  },
  {
    id: "b3",
    name: "Chi nhánh Bình Thạnh",
    address: "78 Nơ Trang Long, BT",
    status: "open",
    lat: 10.803,
    lng: 106.71,
  },
  {
    id: "b4",
    name: "Chi nhánh Gò Vấp",
    address: "34 Quang Trung, GV",
    status: "closed",
    lat: 10.835,
    lng: 106.665,
  },
];

export function useBranch() {
  const [selectedBranch, setSelectedBranch] = useState<Branch>(BRANCHES[0]);
  const [userLocation] = useState(DEFAULT_USER_LOCATION);

  const distance = useMemo(() => {
    return calculateDistance(
      userLocation.lat,
      userLocation.lng,
      selectedBranch.lat,
      selectedBranch.lng
    );
  }, [selectedBranch, userLocation]);

  const selectBranch = useCallback((branchId: string) => {
    const branch = BRANCHES.find((b) => b.id === branchId);
    if (branch) setSelectedBranch(branch);
  }, []);

  return {
    branch: selectedBranch,
    branches: BRANCHES,
    distance,
    userLocation,
    selectBranch,
  };
}

