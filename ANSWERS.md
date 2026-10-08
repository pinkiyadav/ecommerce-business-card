# Written Answers: Web-to-Print Business Cards

## Part 1: Product Configuration

**Product structure.** One configurable product, "Business Card" (SKU `BC`). The customer chooses options and the system looks up the price. It is not set up as six separate products.

**Attributes (descriptive, not selectable).** Product type, print method, finish description, production time.

**Options (customer chooses).**

| Option | Values |
|---|---|
| Size | Standard 3.5" x 2", Square 2.5" x 2.5" |
| Paper | Matte, Glossy |
| Quantity | 100, 500, 1000 |
| Artwork | File upload (required) |

## Part 4: Troubleshooting

### Scenario 1: Pricing displayed on the website is incorrect

**Possible causes**
- The price in the browser (JavaScript) differs from the price in the database, for example after a price change.
- A cached page, CDN copy, or browser cache is showing an old price.
- The wrong price row is matched, such as the glossy price on a matte selection, or a missing quantity tier.
- A bad import or manual edit changed the price data.
- A discount rule, plugin, tax setting, or currency conversion is altering the price.
- Rounding or floating-point errors in the calculation.

**Investigation steps**
1. Ask for the exact selection (size, paper, quantity) and a screenshot showing the displayed price.
2. Reproduce it in a clean browser or private window, to rule out cache.
3. Compare the displayed price with the price table in the database for that paper and quantity.
4. Check whether the cart and checkout show the same price as the product page.
5. Check recent changes: price edits, product imports, plugin updates, and deployments.
7. Clear the cache and test again to see whether the price changes.

**Resolution approach**
- Fix the incorrect price data, or fix the logic that picked the wrong row.
- Clear the page, object, and CDN caches whenever a price changes.
- Find open orders placed at the wrong price, correct them, and contact the customers.
- Tell the reporting customer what happened once it is fixed.

### Scenario 2: Artwork uploads work, but orders fail at checkout

**Possible causes**
- The uploaded file is not connected to the order, because the session or token expired or does not match.
- The file is stored in a temporary folder that is cleaned up before checkout.
- The order insert fails because of a database problem, such as a foreign key error or a missing required value.
- The payment gateway rejects the request or times out.
- A required item option is missing, for example the size or paper value was not saved.
- File permission or storage problems.

**Investigation steps**
1. Read the PHP and web server error logs at the time of the failure. This is the first place to look.
2. Check the browser network tab for the failing request and its status code (500, 413, 504, and so on).
3. Check the database: does the artwork record exist, and does it have the right session token?
4. Try a small file and a large file to see whether size is the problem.
5. Test as a guest and as a logged-in user, and with a different payment method.
6. Check the payment gateway logs for declined or timed-out requests.
7. Check the database for failed or half-created orders.

**Resolution approach**
- Fix the cause found in the logs, such as raising limits or fixing the database error.
- Save the uploaded file permanently at upload time, and link it to the order inside a database transaction. If any step fails, roll back the whole order.
- Show the customer a clear error message and keep their cart, so they do not have to upload again.
- Log checkout failures and set up alerts so the team finds out before customers complain.
- Contact affected customers and recover their orders where possible.

### Scenario 3: The site is much slower after importing 10,000 products

**Possible causes**
- Missing database indexes on SKU, slug, status, category, and foreign keys.
- Slow queries on listing and search pages that load too much data or scan the whole table.
- No pagination, so pages try to load thousands of products at once.
- Missing page, object, or database caching.
- Large unoptimized images.
- Background jobs, search indexing, or cron tasks running during traffic.
- The import left behind revisions, logs, or temporary data.
- The server is too small for the new data size.

**Investigation steps**
1. Compare page load times before and after the import, and find which pages are slow.
2. Turn on the slow query log, and use a profiler (such as Query Monitor or an APM tool) to find the slowest queries.
3. Run `EXPLAIN` on those queries to find missing indexes and full table scans.
4. Check server CPU, memory, and database connections.
5. Check for running cron jobs, queues, or indexers.
6. Check table sizes and look for leftover import data.

**Performance improvements**
- Add indexes for the columns used in filters, sorting, and joins.
- Paginate all product listings and limit what each page loads.
- Add caching: page cache, object cache (Redis or Memcached), and a CDN for static files.
- Resize, compress, and lazy-load images.
- Run imports in batches and outside peak hours.
- Clean up revisions, transients, and logs.


---

## Part 5: Catalog Management

**How I would import these products**
1. Load the CSV into a staging table first, not straight into the live catalog.
2. Validate the staging data .
3. Import into the live tables in a database transaction, using the SKU as the match key. Running the import twice then updates rows instead of creating duplicates.
4. Import everything as draft, not published.
5. Create one Business Card product, and add the three rows (BC100, BC500, BC1000) as quantity price tiers instead of three separate products. The file has no paper type or size, so I would treat it as matte and add glossy and size options separately.
6. Keep an import log with the number of rows added, updated, and rejected. This approach worked well when I migrated products from Magento to WordPress for stsinks.com.

**How I would validate the data before import**
- The required columns (SKU, Name, Price) exist and the file is UTF-8.
- There are no blank rows.
- SKU is not empty, is trimmed, follows the pattern (here `BC` plus a number), and is unique within the file.
- Price is numeric and greater than zero after removing the `$` sign, and uses two decimals.
- The quantity in the name matches the quantity in the SKU (BC500 and "Business Card 500").
- Prices match the pricing table ($20, $80, $140 for matte).

**Checks before publishing live**
- Compare imported products and prices against the source file and the pricing table.
- Confirm the missing data: glossy prices ($25, $100, $180), sizes, and paper types must be added, because the file does not include them.
- Test every combination on the product page: the right price, add to cart, and checkout.
- Check names, URLs, descriptions, images, and SEO fields.
- Check that the cart and order store the correct price and options.
- Test on a staging site first, and take a database backup before publishing.
- Publish only when everything passes, then monitor the first orders.

**How I would handle duplicate SKUs**
- **Detect** them in the staging table with a query that groups by SKU (trimmed, uppercase) and finds counts above one.
- **Against the live catalog:** treat a matching SKU as an update of the existing product, not a new one. Log the old and new price, and send large price changes for review.
- **Prevent them** by putting a `UNIQUE` constraint on SKU in the live table, so a duplicate can never be saved.
