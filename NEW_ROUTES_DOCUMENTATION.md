# SGDII System - New Routes and Actions Documentation

## Date: 2026-02-18
## Sprint: Consolidation and Bug Fixes

---

## New STT Controller Actions

### 1. `SttController::actionIndex()`
**Route:** `/stt` or `/stt/index`  
**Method:** GET  
**Access:** Authenticated users only  
**Description:** Lists all Solicitud de Tema de Tesis (STT) based on user role.

**Behavior by Role:**
- **Admin/Comisión:** Can see all STTs
- **Profesor:** Can see STTs where they are involved (course professor, guide, or reviewer)
- **Alumno:** Can see only their own STTs

**View:** `views/stt/index.php`

**Actions Available in List:**
- View STT details (all users)
- Corregir STT / Edit (owners only, if not resolved)
- Revisar STT / Review (admin/comision only, if pending)

---

### 2. `SttController::actionUpdate($id)`
**Route:** `/stt/update/{id}`  
**Method:** GET, POST  
**Access:** Authenticated users with ownership rights  
**Description:** Allows correction/update of an existing STT that hasn't been resolved yet.

**Permissions:**
- **Admin:** Can update any STT
- **Profesor:** Can update if they are the course professor
- **Alumno:** Can update only their own STTs

**Restrictions:**
- Only STTs in "Enviada" or "En revisión" state can be updated
- Resolved STTs (Aceptada, Rechazada) cannot be modified

**View:** `views/stt/update.php`

**Form Fields Available for Update:**
- Basic information (origen, profesor curso, nota, título)
- Professor assignments (guía, revisor 1, revisor 2)
- Student and career information are read-only

---

## Enhanced STT View Page

### Updated: `SttController::actionView($id)`
**Route:** `/stt/view/{id}`  
**Method:** GET  
**Access:** Authenticated users only

**New Action Buttons:**
- **Corregir STT** (Edit) - Visible to authorized users for non-resolved STTs
- **Revisar STT** (Review) - Visible to admin/comision for pending STTs
- **Volver** (Back) - Returns to STT list

---

## Fixed Statistical Graphs

### Updated: `ReportController::actionEstadisticas()`
**Route:** `/report/estadisticas`  
**Method:** GET  
**Access:** Authenticated users only

**Bug Fixes:**
1. **Tiempos de Resolución Chart:**
   - Now filters professors to only include those with resolved STTs
   - Prevents empty data arrays
   - Shows "no data" message when no professors meet criteria

2. **All Charts:**
   - Added null/empty checks for all chart data
   - Added fallback error messages when data is missing
   - Prevents JavaScript errors from undefined variables

**Chart Error Handling:**
- Modalidades Chart: Shows message if no data
- Evolución Mensual Chart: Shows message if no data
- Modalidad-Estado Chart: Shows message if no data
- Tiempos Resolución Chart: Shows message if no professors with data

---

## Grade Validation and Transformation

### Client-Side Validation (JavaScript)
**Files:**
- `views/stt/create.php`
- `views/stt/update.php`

**Functionality:**
- Automatically detects grades entered in 10-70 range (e.g., 50, 35, 67)
- Converts to 1.0-7.0 range (e.g., 50 → 5.0, 35 → 3.5, 67 → 6.7)
- Shows visual feedback on conversion
- Validates final range (1.0 - 7.0)
- Shows error message for invalid values

**User Experience:**
- User can enter either format: "50" or "5.0"
- Automatic conversion happens on blur (when field loses focus)
- Green feedback message for successful conversion
- Red error message for invalid values

### Server-Side Validation (PHP)
**File:** `models/SttForm.php`

**Validation Rules:**
```php
// Accepts values 1.0-70.0
[['nota'], 'number', 'min' => 1.0, 'max' => 70.0]

// Automatic transformation filter
[['nota'], 'filter', 'filter' => function($value) {
    if ($value >= 10 && $value <= 70) {
        return $value / 10;  // Convert to 1.0-7.0 range
    }
    return $value;
}]

// Final validation
[['nota'], 'number', 'min' => 1.0, 'max' => 7.0]
```

**Safety:**
- Accepts input in both formats
- Performs transformation before final validation
- Ensures data consistency in database

---

## Navigation Updates

### Main Navigation Menu (`views/layouts/main.php`)

**New Links Added:**
1. **Solicitudes STT** (`/stt/index`) - Visible to all authenticated users
2. **Tesis** (`/tesis/index`) - Visible to admin, professors, and students

**Link Order:**
1. Inicio (Home)
2. Solicitudes STT (New)
3. Nueva STT (if can create)
4. Tesis (New)
5. Gestionar Evaluación (if comision)
6. Reportes

---

## WebSocket Configuration Status

### Verified Configurations:
- **Server Port:** 8080 (WebSocketService.php, WebsocketController.php)
- **Client Connection:** Dynamic port 8080 (websocket-client.js)
- **Protocol:** ws:// for HTTP, wss:// for HTTPS

**No Issues Found:**
- No hardcoded port 80 references
- Port configuration is consistent across all files
- WebSocket client correctly uses window.location.hostname with port 8080

---

## Testing Recommendations

### Manual Testing Checklist:

1. **STT List View:**
   - [ ] Access /stt/index as different user roles
   - [ ] Verify correct filtering by role
   - [ ] Test action buttons visibility

2. **STT Update:**
   - [ ] Try updating STT in different states
   - [ ] Verify permission checks work
   - [ ] Test as different user roles

3. **Grade Validation:**
   - [ ] Enter grade as "50", verify converts to "5.0"
   - [ ] Enter grade as "35", verify converts to "3.5"
   - [ ] Enter grade as "5.0", verify stays as "5.0"
   - [ ] Enter invalid grade (e.g., "80"), verify error message
   - [ ] Submit form with converted grade, verify saves correctly

4. **Statistical Graphs:**
   - [ ] Access /report/estadisticas
   - [ ] Verify all charts load or show appropriate messages
   - [ ] Test with empty database
   - [ ] Test with populated database

5. **Navigation:**
   - [ ] Verify new navigation links appear
   - [ ] Test navigation flow: List → View → Edit → List
   - [ ] Test navigation flow: List → View → Review

6. **WebSocket:**
   - [ ] Verify WebSocket connects successfully
   - [ ] Check browser console for errors
   - [ ] Test real-time notifications

---

## Security Considerations

### Access Control:
- All new actions require authentication
- Update action has role-based permission checks
- Students can only update their own STTs
- Resolved STTs cannot be modified

### Data Validation:
- Client-side validation for user experience
- Server-side validation for security
- Grade transformation happens on both sides
- SQL injection prevention via Yii2 ORM

---

## Future Enhancements (Not in Current Scope)

1. Add bulk operations for STT management
2. Add export functionality for STT list
3. Add filtering and sorting to STT list view
4. Add pagination for large STT lists
5. Add audit trail for STT modifications
6. Add email notifications for STT updates

---

## Related Files Modified

### Controllers:
- `controllers/SttController.php` - Added actionIndex() and actionUpdate()
- `controllers/ReportController.php` - Fixed getTiemposResolucion()

### Views:
- `views/stt/index.php` - New list view
- `views/stt/view.php` - Added action buttons
- `views/stt/update.php` - New update view
- `views/stt/create.php` - Added grade validation
- `views/report/estadisticas.php` - Fixed chart error handling
- `views/layouts/main.php` - Added navigation links

### Models:
- `models/SttForm.php` - Added grade transformation validation

---

## Summary

This update addresses all critical issues identified in the consolidation sprint:
- ✅ Fixed broken/missing STT links and actions
- ✅ Added review and correction functionality
- ✅ Fixed statistical graph errors
- ✅ Implemented grade validation and transformation
- ✅ Verified WebSocket configuration
- ✅ Enhanced navigation flow

All changes maintain backward compatibility and follow existing code patterns.
