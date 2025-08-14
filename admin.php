<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
  header("Location: login.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Portal — Canteen</title>
<link rel="stylesheet" href="admin.css">
</head>
<body>
  <div class="topbar">
    <h2>👨‍💼 Admin Portal</h2>
    <div>
      <a class="btn" href="index.html">← Back to Student</a>
      <a class="btn btn-danger" href="logout.php">Logout</a>
    </div>
  </div>

  <div class="wrap">
    <section class="card">
      <h3>➕ Add Menu Item</h3>
      <div class="row">
        <label>Name</label><input id="item-name" type="text" placeholder="e.g., Nasi Lemak">
      </div>
      <div class="row">
        <label>Description</label><textarea id="item-description" rows="2" placeholder="Brief description"></textarea>
      </div>
      <div class="row">
        <label>Price (RM)</label><input id="item-price" type="number" step="0.01" placeholder="5.50">
      </div>
      <div class="row">
        <label>Category</label>
        <select id="item-category">
          <option>Main Course</option><option>Beverages</option><option>Snacks</option><option>Desserts</option>
        </select>
      </div>
      <button class="btn" onclick="addMenuItem()">Add Item</button>
    </section>

    <section class="card">
      <h3>📝 Manage Menu</h3>
      <div id="admin-menu" class="grid"></div>
    </section>

    <section class="card">
      <h3>📊 Order Management</h3>
      <div id="admin-orders"></div>
    </section>
  </div>

<script>
async function fetchMenu(){
  const r = await fetch('get_menu.php'); return (await r.json()).menu || [];
}
async function renderMenu(){
  const menu = await fetchMenu();
  const el = document.getElementById('admin-menu');
  if(!menu.length){ el.innerHTML = '<p>No menu items.</p>'; return; }
  el.innerHTML = menu.map(m=>`
    <div class="item">
      <h4>${m.name}</h4>
      <p>${m.description||''}</p>
      <p><b>RM ${Number(m.price).toFixed(2)}</b> · ${m.category}</p>
      <button class="btn btn-danger" onclick="deleteItem(${m.id})">Delete</button>
    </div>
  `).join('');
}
async function addMenuItem(){
  const body = new URLSearchParams({
    name: document.getElementById('item-name').value.trim(),
    description: document.getElementById('item-description').value.trim(),
    price: document.getElementById('item-price').value,
    category: document.getElementById('item-category').value
  });
  const r = await fetch('add_item.php', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body });
  const j = await r.json();
  if(j.success){ alert('Added!'); renderMenu(); } else { alert(j.message||'Failed'); }
}
async function deleteItem(id){
  if(!confirm('Delete this item?')) return;
  const r = await fetch('delete_item.php?id='+id, { method:'GET' });
  const j = await r.json();
  if(j.success){ renderMenu(); } else { alert('Failed'); }
}
async function renderOrders(){
  const r = await fetch('get_orders.php'); const j = await r.json();
  const orders = j.orders||[];
  const el = document.getElementById('admin-orders');
  if(!orders.length){ el.innerHTML='<p>No orders yet.</p>'; return; }
  el.innerHTML = orders.map(o=>`
    <div class="order">
      <h4>#${o.id} — ${o.student_name}</h4>
      <p><b>Status:</b> ${o.status} · <b>Time:</b> ${o.created_at}</p>
      <ul>${o.items.map(it=>`<li>${it.name} x${it.quantity} (RM ${(it.price*it.quantity).toFixed(2)})</li>`).join('')}</ul>
      <p><b>Total:</b> RM ${Number(o.total).toFixed(2)}</p>
      <div class="actions">
        <button class="btn" onclick="setStatus(${o.id},'preparing')">Preparing</button>
        <button class="btn btn-success" onclick="setStatus(${o.id},'ready')">Ready</button>
        <button class="btn" onclick="setStatus(${o.id},'completed')">Completed</button>
      </div>
    </div>
  `).join('');
}
async function setStatus(id,status){
  const r = await fetch('update_status.php', {
    method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body: new URLSearchParams({ id, status })
  });
  const j = await r.json();
  if(j.success){ renderOrders(); } else { alert('Failed to update'); }
}
renderMenu(); renderOrders();
</script>
</body>
</html>
