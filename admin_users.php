<?php
session_start();

if(!isset($_SESSION["user_id"]) || $_SESSION["is_admin"] != 1){
    header("Location: login.php");
    exit();
}

include "config.php";
include "security.php";
include "includes/header.php";

$search = trim($_GET["search"] ?? "");

$stmt = $conn->prepare("
SELECT
    users.id,
    users.username,
    users.email,
    users.is_admin,
    users.two_factor_enabled,
    COUNT(orders.id) AS total_orders,
    COALESCE(SUM(orders.total_price),0) AS total_spent
FROM users
LEFT JOIN orders
ON users.id = orders.user_id
WHERE
users.username LIKE CONCAT('%', ?, '%')
OR users.email LIKE CONCAT('%', ?, '%')
GROUP BY users.id
ORDER BY users.username
");

$stmt->bind_param("ss",$search,$search);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mt-4">

<h2 class="mb-4">👥 User Management</h2>

<form method="GET" class="row g-2 mb-4">

<div class="col-md-6">
<input
type="text"
name="search"
class="form-control"
placeholder="Search username or email..."
value="<?php echo clean($search); ?>">
</div>

<div class="col-auto">
<button class="btn btn-primary">
Search
</button>
</div>

</form>

<div class="table-responsive">

<table class="table table-hover align-middle">

<thead class="table-dark">

<tr>
<th>ID</th>
<th>Username</th>
<th>Email</th>
<th>Admin</th>
<th>2FA</th>
<th>Orders</th>
<th>Total Spent</th>
</tr>

</thead>

<tbody>

<?php if($result->num_rows==0){ ?>

<tr>

<td colspan="7" class="text-center">

No users found.

</td>

</tr>

<?php } ?>

<?php while($user=$result->fetch_assoc()){ ?>

<tr>

<td>
<?php echo clean($user["id"]); ?>
</td>

<td>
<?php echo clean($user["username"]); ?>
</td>

<td>
<?php echo clean($user["email"]); ?>
</td>

<td>

<?php if($user["is_admin"]){ ?>

<span class="badge bg-danger">Admin</span>

<?php }else{ ?>

<span class="badge bg-secondary">User</span>

<?php } ?>

</td>

<td>

<?php if($user["two_factor_enabled"]){ ?>

<span class="badge bg-success">Enabled</span>

<?php }else{ ?>

<span class="badge bg-warning text-dark">Disabled</span>

<?php } ?>

</td>

<td>

<?php echo clean($user["total_orders"]); ?>

</td>

<td>

€ <?php echo number_format($user["total_spent"],2); ?>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

<?php
$stmt->close();
include "includes/footer.php";
?>