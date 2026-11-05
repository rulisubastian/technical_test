# Decisions — candidate must complete honestly

1. **Concurrent stock:** Review `backend/app/Services/OrderService.php` and `StockService.php`. Explain the row-locking and transaction boundary in your own words.
2. **Structure:** Explain the HTTP / application / persistence / async separation and what you would change as the system grows.
3. **Bug encountered:** Record a real bug you encountered, reproduction steps, evidence/log/test used to find it, root cause and fix. Do not invent this section.
4. **Remaining weaknesses:** Run the test suite and list actual failures, gaps, production hardening and next steps.
