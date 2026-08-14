# PHP MVC Methods
                             
 1. indexAction - Home page (default route)              
 2. searchAction - PLZ autocomplete search (POST /search)
 3. calculateAction - Calculate valuation (POST /calculate)          
 4. saveAction - Save valuation (POST /save)                  
 5. showAction - Get single valuation (GET /show/{id})         
 6. editAction - Edit valuation (PUT /edit/{id})              
 7. deleteAction - Delete valuation (DELETE /delete/{id})       
 8. listAction - Get all valuations (GET /list)

How Routing works in this App:
```php             
 - $router->add('{controller}/{action}');   
 - $router->add('{controller}/{id:\d+}/{action}');     
 - $router->add('', ['controller' => 'Homes', 'action' => 'index']);
 - $router->add('homes/edit/{id:\d+}', ['controller' => 'Homes', 'action' => 'edit']);                                     
```
Here's what the Homes.php controller needs:

| Route | Method | HTTP | Description |
| --- | --- | --- | --- |
| `/` | `index` | GET | Home page (default) |
| `/homes/search` | `search` | POST | PLZ autocomplete |
| `/homes/calculate` | `calculate` | POST | Calculate valuation |
| `/homes/save` | `save` | POST | Save valuation |
| `/homes/show/{id}` | `show` | GET | Single valuation |
| `/homes/edit/{id}` | `edit` | PUT | Update valuation |
| `/homes/delete/{id}` | `delete` | DELETE | Delete valuation |
| `/homes/list` | `list` | GET | All valuations |

---

## Method example

All methods have to run in php Backend. Javascript is only used for frontend validation and has to be backed up with php validation. Javascript has to be created as module inside public/js/modules/ and imported into public/js/app.js.


Enable user to search database for houses or appartements

**Controller**

The controller is responsible for handling the request from the client. It checks if a keyword was provided in the POST request and then queries the database for sales that match the keyword.


```php
    /**
     * Search sales for user
     *
     *@return void
     */
    public function queryAction()
    {
        if (isset($_POST['keyword'])) {
            $data = $_POST['keyword'];
            $sales = Sale::querySales($data);

            View::renderTemplate('Sales/query.html', [
                'sales' => $sales
            ]);
        } else {
            $this->redirect('/');
        }
    }
```

**Model**

```php
  /**
   * Query Sale model and return matches
   * 
   * let user choose what content he searches with radio buttons
   *
   *@return mixed Sales object if found, otherwise false
   */
  public static function querySales(string $keyword)
  {
    $pattern = '%' . $keyword . '%';
    $db = static::getDB();
    if (isset($_POST['radio-choice'])) {
      if ($_POST['radio-choice'] == 'sale') {
        $sql = "SELECT * 
            FROM `sales` AS p1 
              INNER JOIN `sales_images` AS p2 
                  ON p1.main_uuid = p2.uuid 
              WHERE p1.is_active = 1 
                AND (
                    p1.title LIKE :pattern 
                    OR p1.object_type LIKE :pattern 
                    OR p1.content LIKE :pattern 
                    OR p1.content2 LIKE :pattern 
                    OR p1.obj_address LIKE :pattern
                );
              ";
      } else if ($_POST['radio-choice'] == 'rent') {
        $sql = "SELECT * 
        FROM `rentals` AS p1 
          INNER JOIN `rentals_images`AS p2 
              ON p1.main_uuid = p2.uuid 
              WHERE p1.is_active = 1 
              AND (
                  p1.title LIKE :pattern 
                  OR p1.subtitle LIKE :pattern 
                  OR p1.description LIKE :pattern 
                  OR p1.location LIKE :pattern
              );
              ";
      }
    }
    $stmt = $db->prepare($sql);

    $stmt->execute([':pattern' => $pattern]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $result;
  }
  ```
  **View**

The Request is sent to the server using a POST request with the `action` attribute set to `/sales/query`. The form includes input fields for the search pattern and radio buttons for selecting the type of property (sale or rent).


```html
<form action="/sales/query" method="POST" id="queryForm" class="queryform" autocomplete="on">
```