/**
 * Haversine formula - tính khoảng cách giữa 2 điểm trên bề mặt Trái Đất
 * @returns khoảng cách tính bằng km, làm tròn 1 chữ số thập phân
 */
export function calculateDistance(
  lat1: number,
  lng1: number,
  lat2: number,
  lng2: number
): number {
  const R = 6371; // Bán kính Trái Đất (km)
  const dLat = toRad(lat2 - lat1);
  const dLng = toRad(lng2 - lng1);
  const a =
    Math.sin(dLat / 2) * Math.sin(dLat / 2) +
    Math.cos(toRad(lat1)) *
      Math.cos(toRad(lat2)) *
      Math.sin(dLng / 2) *
      Math.sin(dLng / 2);
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  const distance = R * c;
  return Math.round(distance * 10) / 10;
}

function toRad(deg: number): number {
  return (deg * Math.PI) / 180;
}

/**
 * Ước tính thờigian giao hàng dựa trên khoảng cách
 * - Cơ sở: 5 phút chuẩn bị + 3 phút/km
 */
export function estimateDeliveryTime(distanceKm: number): string {
  const prepTime = 5;
  const perKm = 3;
  const totalMinutes = Math.ceil(prepTime + distanceKm * perKm);
  if (totalMinutes < 10) return "~10 phút";
  return `~${totalMinutes} phút`;
}

/**
 * Tính phí ship dựa trên khoảng cách
 * - Dưới 1km: 10,000đ
 * - 1-3km: 15,000đ
 * - 3-5km: 20,000đ
 * - Trên 5km: 25,000đ
 */
export function calculateShippingFee(distanceKm: number): number {
  if (distanceKm <= 1) return 10000;
  if (distanceKm <= 3) return 15000;
  if (distanceKm <= 5) return 20000;
  return 25000;
}

