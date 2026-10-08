# Business Cards: Core PHP

## Run it
1. Create the database and load the SQL: from database/ecommerce.sql
   
2. Edit `config.php` with your MySQL user and password.
3. Make sure `uploads/` is writable, then start the server:

## Files
- `index.php`: product page (options and prices come from the database)
- `add-to-cart.php`: validates input and artwork, stores the file, prices 
- `cart.php`, `pcheckout.php`: cart and order creation (transaction)
- `database/`: schema, seed, duplicate sku query added 2 methods
- `ANSWERS.md`: written answers for Parts 1, 4, 5
