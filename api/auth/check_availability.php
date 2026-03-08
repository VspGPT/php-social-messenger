diff --git a/api/auth/check_availability.php b/api/auth/check_availability.php
index a10d8bf6e669fb7627a116a3f9b2ca2ddbf556d8..0cf6be97892c315942af69ef615a41bf9cf3773f 100644
--- a/api/auth/check_availability.php
+++ b/api/auth/check_availability.php
@@ -1,29 +1,45 @@
 <?php
 header('Access-Control-Allow-Origin: *');
 header('Access-Control-Allow-Headers: Content-Type, Authorization');
 header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
 header('Content-Type: application/json');
 
 include '../../config.php';
 $query = new Database();
 
-$response = ['exists' => false];
+$response = [
+    'status' => 'error',
+    'exists' => false,
+    'message' => 'Invalid request'
+];
+
+if ($_SERVER['REQUEST_METHOD'] === 'POST') {
+    $field = null;
+    $value = null;
 
-if ($_SERVER['REQUEST_METHOD'] == 'POST') {
     if (isset($_POST['email'])) {
-        $email = $_POST['email'];
-        $result = $query->select('users', 'email', 'email = ?', [$email], 's');
-        if (!empty($result)) {
-            $response['exists'] = true;
-        }
+        $field = 'email';
+        $value = trim($_POST['email']);
+    } elseif (isset($_POST['username'])) {
+        $field = 'username';
+        $value = trim($_POST['username']);
     }
-    if (isset($_POST['username'])) {
-        $username = $_POST['username'];
-        $result = $query->select('users', 'username', 'username = ?', [$username], 's');
-        if (!empty($result)) {
-            $response['exists'] = true;
+
+    if ($field !== null && $value !== '') {
+        $result = $query->select('users', $field, "$field = ?", [$value], 's');
+
+        if (is_string($result)) {
+            $response['status'] = 'error';
+            $response['message'] = 'Availability check failed';
+        } else {
+            $response['status'] = 'success';
+            $response['exists'] = !empty($result);
+            $response['message'] = 'Availability check completed';
         }
+    } else {
+        $response['status'] = 'error';
+        $response['message'] = 'Missing required field';
     }
 }
 
 echo json_encode($response);
