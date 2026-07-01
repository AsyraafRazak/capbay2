# CapBay Auto - Software Engineer Technical Assessment

This project is a high-performance **Laravel 12** web application built for **CapBay Auto Sdn. Bhd.** to manage test drive registrations, evaluate promotional discount eligibility for their latest AI car model (**CapBay Vroom**), and execute registration state transitions via an agent dashboard.

---

## 🚀 Setup and Run Instructions

### Prerequisites
- **PHP 8.2+**
- **Composer**

### Installation Steps

1. **Clone or Extract** the project source into your server directory (e.g., `c:\xampp\htdocs\capbay2`).
2. **Install Dependencies** (if deploying clean):
   ```bash
   composer install
   ```
3. **Database Migration and Seeding**:
   This app utilizes SQLite. To create the database, run migrations, and seed **50,000 mock registrations** (which runs in under 15 seconds):
   ```bash
   php artisan migrate:fresh --seed
   ```
4. **Run Server**:
   Start the local development server:
   ```bash
   php artisan serve
   ```
5. **Access Application**:
   - **Customer Portal (Registration Form)**: Visit [http://127.0.0.1:8000](http://127.0.0.1:8000) or `/register`
   - **Sales Agent Portal (Dashboard & Look-up)**: Visit [http://127.0.0.1:8000/agent](http://127.0.0.1:8000/agent)

### Run Automated Tests
Execute the PHPUnit feature tests verifying state transitions, calculations, and queue shifting:
```bash
php artisan test
```

---

## 📝 Technical Decisions & Assumptions

### 1. Data Type for Money
- **Choice**: Stored as **Integer representing cents** (`unsignedBigInteger` in database, cast to `integer` on model).
- **Reasoning**: Floating point numbers (like PHP floats or SQL double/float) suffer from binary representation limitations under IEEE-754 standards (e.g. `0.1 + 0.2 = 0.30000000000000004`). In financial systems, even fractional cents discrepancies can compound into severe audit failures. Storing money in cents (as an integer) eliminates rounding errors. The application formats these integers to human-readable RM format only at the display level (e.g. `RM 200,000.00`).

### 2. Customer B / Customer C Queue Scenario
- **Scenario Decision**: If Customer B (2nd to register) cancels, **Customer C (11th to register) becomes eligible** for the promotion.
- **Reasoning**: 
  - *Business Conversion*: A marketing promotion aims to sell 10 discounted cars. If a customer cancels, leaving that discount slot unused would result in lost sales velocity.
  - *Dynamic Queue Design*: Real-world waitlists automatically elevate the next active customer. By evaluating eligibility dynamically based on the first 10 *non-cancelled* registrations (`status != 'cancelled'`), Customer C shifts up the queue and receives the discount slot.
  - *Code Implementation*: Eligible IDs are fetched dynamically via:
    ```php
    Registration::where('car_model', 'CapBay Vroom')
        ->where('status', '!=', 'cancelled')
        ->orderBy('id', 'asc')
        ->limit(10)
        ->pluck('id');
    ```

### 3. Minimum Down Payment for Promotion
- **Clarification**: The brief states customers must pay "at least 10% of the down payment". This was initially misread as 10% of the standard down payment (10% of RM 20,000 = RM 2,000). Upon re-reading, the correct interpretation is **10% of the car price directly** — i.e. 10% of RM 200,000 = **RM 20,000** minimum.
- This has been corrected in the model (`$minDownPaymentCents = 2000000`) and reflected in the agent dashboard UI.

### 4. Clear State Transitions
We configured a state machine pattern with valid transition paths:
- `registered` &rarr; `test_drive_scheduled` | `cancelled`
- `test_drive_scheduled` &rarr; `test_drive_completed` | `cancelled`
- `test_drive_completed` &rarr; `purchased` | `cancelled`
- `purchased` (Terminal state)
- `cancelled` (Terminal state)
Invalid transitions (e.g., straight from `registered` to `purchased` or reversing from `cancelled` back to `registered`) are rejected at the database/model layer with an `InvalidStateTransitionException` and handled gracefully in the UI.

---

## AI Tools Self-Correction Note
- **AI Tool Used**: Antigravity (Gemini 3.5 Flash / Medium) for setup, Kiro for specific tasks
- **Correction Example**: 
  The minimum down payment threshold for the promotion was initially implemented as **RM 2,000** — which was 10% of the standard down payment (RM 20,000), itself 10% of the car price. This was a misinterpretation. The brief states "paid for at least 10% of the down payment", where the down payment refers to 10% of RM 200,000 = RM 20,000. The correct minimum is therefore **RM 20,000**, not RM 2,000. The model constant was corrected from `200000` cents to `2000000` cents, and the agent dashboard checklist label and helper text were updated to match.

