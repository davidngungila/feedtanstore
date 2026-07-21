# Delivery Fee Calculation Report

**Generated:** July 21, 2026  
**System:** FeedTan Store - Online Sales Module

---

## Overview

The delivery fee for online sales is calculated dynamically based on distance between the store and customer location, order subtotal, and configurable store settings.

---

## Implementation Location

- **Model:** `app/Models/StoreSetting.php`
- **Method:** `calculateDeliveryFee()` (lines 117-166)
- **Controller:** `app/Http/Controllers/OnlineOrderController.php`

---

## Configuration Settings

The following settings are stored in the `store_settings` table and can be configured via the admin panel at `/store/settings`:

| Setting | Description | Default Value |
|---------|-------------|---------------|
| `delivery_base_fee` | Base delivery fee charged for all orders | 2,000 TZS |
| `delivery_per_km_rate` | Additional fee per kilometer of distance | 400 TZS |
| `delivery_free_threshold` | Order subtotal amount for free delivery | 50,000 TZS |
| `delivery_use_zone_pricing` | Enable zone-based pricing instead of distance-based | false |
| `delivery_zone_config` | JSON configuration for delivery zones | null |
| `store_latitude` | Store location latitude | -3.3430 (Moshi, Tanzania) |
| `store_longitude` | Store location longitude | 37.3507 (Moshi, Tanzania) |

### How to Configure

1. Navigate to `/store/settings` in your admin panel
2. Scroll to the **Delivery Fee Settings** section
3. Update the following fields:
   - **Base Delivery Fee**: Enter the base fee in TZS
   - **Per-Kilometer Rate**: Enter the rate per km in TZS
   - **Free Delivery Threshold**: Enter the order subtotal amount for free delivery
4. Optionally enable **Zone Pricing** and configure delivery zones in JSON format
5. Click **Save Settings** to persist changes to the database

### Zone Pricing Configuration

If zone pricing is enabled, configure zones using JSON format:

```json
[
  {
    "name": "Zone 1",
    "min_km": 0,
    "max_km": 3,
    "fee": 2500
  },
  {
    "name": "Zone 2",
    "min_km": 3.1,
    "max_km": 6,
    "fee": 3500
  },
  {
    "name": "Zone 3",
    "min_km": 6.1,
    "max_km": 999,
    "fee": 5000
  }
]
```

Each zone defines:
- `name`: Zone identifier
- `min_km`: Minimum distance in kilometers
- `max_km`: Maximum distance in kilometers
- `fee`: Fixed delivery fee for this zone

---

## Calculation Algorithm

### Step 1: Distance Calculation

The system uses the **Haversine formula** to calculate the great-circle distance between two points on a sphere (Earth):

```php
public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
{
    $earthRadius = 6371; // km

    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat / 2) * sin($dLat / 2) +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
        sin($dLon / 2) * sin($dLon / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $earthRadius * $c;
}
```

**Result:** Distance in kilometers

---

### Step 2: Free Delivery Check

If the order subtotal meets or exceeds the free delivery threshold:

```
IF order_subtotal >= free_threshold THEN
    delivery_fee = 0
    RETURN FREE
END IF
```

**Example:** Order subtotal of 55,000 TZS → FREE delivery

---

### Step 3: Zone-Based Pricing (Optional)

If `delivery_use_zone_pricing` is enabled and zones are configured:

```json
[
  {
    "min_km": 0,
    "max_km": 5,
    "fee": 3000
  },
  {
    "min_km": 5.1,
    "max_km": 10,
    "fee": 5000
  },
  {
    "min_km": 10.1,
    "max_km": 999,
    "fee": 8000
  }
]
```

**Logic:**
1. Find zone where distance falls within `min_km` and `max_km`
2. Return the fixed fee for that zone
3. If no zone matches, use the last zone's fee or base fee

---

### Step 4: Distance-Based Pricing (Default)

If zone pricing is not enabled, use the standard formula:

```
delivery_fee = base_fee + (distance × per_km_rate)
```

**Examples:**
- 2 km distance: 2,000 + (2 × 400) = 2,800 TZS
- 5 km distance: 2,000 + (5 × 400) = 4,000 TZS
- 10 km distance: 2,000 + (10 × 400) = 6,000 TZS

---

## API Endpoints

### Calculate Delivery Fee (Real-time)

**Endpoint:** `POST /api/calculate-delivery-fee`

**Request:**
```json
{
  "delivery_latitude": -3.3500,
  "delivery_longitude": 37.3600,
  "subtotal": 25000
}
```

**Response:**
```json
{
  "success": true,
  "delivery_fee": 2800,
  "distance": 2.5,
  "formatted_delivery_fee": "TZS 2,800",
  "formatted_distance": "2.50 km",
  "is_free": false
}
```

---

## Usage in Application

The delivery fee calculation is called from multiple points in `OnlineOrderController.php`:

1. **Order Listing** (line 159): Display calculated fees in orders table
2. **Order Details** (line 332): Show fee breakdown on order page
3. **API Endpoint** (line 643): Real-time calculation for checkout
4. **Order Placement** (line 699): Auto-calculate when coordinates provided

---

## Code Reference

**File:** `app/Models/StoreSetting.php`

```php
public function calculateDeliveryFee(float $customerLat, float $customerLon, float $orderSubtotal): array
{
    // Set default values
    $baseFee = (float) ($this->delivery_base_fee ?? 2000);
    $perKmRate = (float) ($this->delivery_per_km_rate ?? 400);
    $freeThreshold = (float) ($this->delivery_free_threshold ?? 50000);
    
    $storeLat = (float) ($this->store_latitude ?? -3.3430);
    $storeLon = (float) ($this->store_longitude ?? 37.3507);
    
    // Calculate distance
    $distance = $this->calculateDistance($storeLat, $storeLon, $customerLat, $customerLon);

    // Free delivery check
    if ($orderSubtotal >= $freeThreshold) {
        return ['fee' => 0, 'distance' => $distance];
    }

    // Zone-based pricing
    if ($this->delivery_use_zone_pricing && $this->delivery_zone_config) {
        foreach ($this->delivery_zone_config as $zone) {
            if ($distance >= $zone['min_km'] && $distance <= $zone['max_km']) {
                return ['fee' => (float) $zone['fee'], 'distance' => $distance];
            }
        }
        $lastZone = end($this->delivery_zone_config);
        $fee = $lastZone ? (float) $lastZone['fee'] : $baseFee;
        return ['fee' => $fee, 'distance' => $distance];
    }

    // Distance-based pricing
    $fee = $baseFee + ($distance * $perKmRate);
    return ['fee' => max(0, round($fee, 2)), 'distance' => $distance];
}
```

---

## Summary

The delivery fee system is flexible and supports:
- ✅ Distance-based pricing with configurable base fee and per-km rate
- ✅ Free delivery threshold based on order subtotal
- ✅ Optional zone-based pricing for fixed-fee delivery areas
- ✅ Real-time calculation via API for checkout experience
- ✅ Automatic fallback to default values if settings not configured

**Current Configuration (Defaults):**
- Base Fee: 2,000 TZS
- Per KM: 400 TZS
- Free Threshold: 50,000 TZS
- Store Location: Moshi, Tanzania
