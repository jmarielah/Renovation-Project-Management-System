<?php
session_start();
if (!isset($_SESSION['userID'])) { header("Location: login.php"); exit(); }
include 'db_connection.php';

$project_id = isset($_GET['project_id']) ? intval($_GET['project_id']) : 0;
$verify = $conn->query("SELECT project_name FROM projects WHERE project_id = $project_id AND is_active = 1 LIMIT 1");
if (!$verify || $verify->num_rows === 0) { header("Location: dashboard.php"); exit(); }
$project = $verify->fetch_assoc();

$existing_costs_query = $conn->prepare("SELECT * FROM comprehensive_costs WHERE project_id = ? ORDER BY id ASC");
$existing_costs_query->bind_param("i", $project_id);
$existing_costs_query->execute();
$existing_costs_result = $existing_costs_query->get_result();

$existing_items_array = [];
while ($row = $existing_costs_result->fetch_assoc()) {
    $existing_items_array[] = [
        'id' => $row['id'],
        'cost_name' => $row['cost_name'],
        'cost_type' => $row['cost_type'],
        'calculated_total' => floatval($row['calculated_total']),
        'material_type' => $row['material_type'],
        'unit' => $row['unit'],
        'quantity' => $row['quantity'],
        'price_per_unit' => $row['price_per_unit'],
        'furniture_type' => $row['furniture_type'],
        'price' => $row['price'],
        'wage_per_day' => $row['wage_per_day'],
        'estimated_days' => $row['estimated_days'],
        'other_type' => $row['other_type']
    ];
}
$existing_items_json = json_encode($existing_items_array);

$edit_id = isset($_GET['edit_id']) ? intval($_GET['edit_id']) : 0;
$edit_item_json = "null";

if ($edit_id > 0) {
    $edit_query = $conn->prepare("SELECT * FROM comprehensive_costs WHERE id = ? AND project_id = ? LIMIT 1");
    $edit_query->bind_param("ii", $edit_id, $project_id);
    $edit_query->execute();
    $edit_res = $edit_query->get_result();
    if ($edit_res->num_rows > 0) {
        $edit_item_json = json_encode($edit_res->fetch_assoc());
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['finalize_sheet']) && isset($_POST['cost_items_json'])) {
        $items = json_decode($_POST['cost_items_json'], true);
        $conn->begin_transaction();
        try {
            $clear_stmt = $conn->prepare("DELETE FROM comprehensive_costs WHERE project_id = ?");
            $clear_stmt->bind_param("i", $project_id);
            $clear_stmt->execute();

            $clear_auto_tasks = $conn->prepare("DELETE FROM project_updates WHERE project_id = ? AND status = 'Resource Acquisition' AND update_note LIKE 'Procure:%'");
            $clear_auto_tasks->bind_param("i", $project_id);
            $clear_auto_tasks->execute();

            if (!empty($items)) {
                foreach ($items as $item) {
                    $cost_name = trim($item['cost_name']);
                    $cost_type = $item['cost_type'];
                    $calculated_total = floatval($item['calculated_total']);
                    
                    $material_type = !empty($item['material_type']) ? $item['material_type'] : null;
                    $unit = !empty($item['unit']) ? $item['unit'] : null;
                    $price_per_unit = !empty($item['price_per_unit']) ? floatval($item['price_per_unit']) : null;
                    $furniture_type = !empty($item['furniture_type']) ? $item['furniture_type'] : null;
                    $price = !empty($item['price']) ? floatval($item['price']) : null;
                    $quantity = !empty($item['quantity']) ? intval($item['quantity']) : null;
                    $wage_per_day = !empty($item['wage_per_day']) ? floatval($item['wage_per_day']) : null;
                    $estimated_days = !empty($item['estimated_days']) ? intval($item['estimated_days']) : null;
                    $other_type = !empty($item['other_type']) ? $item['other_type'] : null;

                    $stmt = $conn->prepare("INSERT INTO comprehensive_costs (project_id, cost_name, cost_type, material_type, unit, price_per_unit, furniture_type, quantity, price, wage_per_day, estimated_days, other_type, calculated_total) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("issssdidddisd", $project_id, $cost_name, $cost_type, $material_type, $unit, $price_per_unit, $furniture_type, $quantity, $price, $wage_per_day, $estimated_days, $other_type, $calculated_total);
                    $stmt->execute();
                }
            }
            $conn->commit();
            header("Location: tasks.php?project_id=" . $project_id);
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            die("Transaction error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'head.php'; ?>
<body style="background-color: #f7f3ee; color: #4a3e3d; font-family: system-ui, sans-serif;">
    <div class="container py-5" style="max-width: 1000px;">
        <div class="mb-3">
            <a href="tasks.php?project_id=<?= $project_id ?>" class="text-decoration-none small fw-medium" style="color: #dfa875;"><i class="bi bi-arrow-left me-1"></i> Back to Checklist</a>
        </div>
        
        <div class="row g-4">
            <div class="col-12 col-md-5">
                <div class="card border-0 shadow-sm rounded-3 p-4 bg-white sticky-top" style="top:24px;">
                    <h5 class="fw-bold mb-1" id="formActionHeading" style="color: #3d2621;"><i class="bi bi-calculator me-2" style="color: #dfa875;"></i>Add Item</h5>
                    <form id="itemDraftForm" onsubmit="addItemToSheet(event)">
                        <input type="hidden" id="editing_array_index" value="-1">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Item Name</label>
                            <input type="text" id="cost_name" class="form-control form-control-sm rounded-2" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Cost Type</label>
                            <select id="costTypeSelect" class="form-select form-select-sm rounded-2" required>
                                <option value="" disabled selected>-- Select Type --</option>
                                <option value="material">Material</option>
                                <option value="furniture">Furniture</option>
                                <option value="labor">Labor</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div id="dynamicFormBlocks" class="mb-3"></div>
                        <div class="p-2 rounded-2 text-end mb-3" style="background-color: #fdfaf6; border-left: 3px solid #dfa875;">
                            <small class="text-muted text-uppercase fw-bold d-block" style="font-size: 0.6rem;">Item Total</small>
                            <h5 class="fw-bold m-0" style="color: #3d2621;" id="runningTotalDisplay">P0.00</h5>
                        </div>
                        <button type="submit" class="btn btn-sm w-100 text-white fw-medium py-2 rounded-2" style="background-color: #dfa875; border:none;" id="formSubmitBtn">
                            <i class="bi bi-plus-circle me-1"></i> Add to Worksheet
                        </button>
                        <button type="button" class="btn btn-sm btn-light w-100 mt-2 d-none" id="cancelEditBtn" onclick="resetFormToNormal()">Cancel Edit</button>
                    </form>
                </div>
            </div>

            <div class="col-12 col-md-7">
                <div class="card border-0 shadow-sm rounded-3 p-4 bg-white h-100 d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="fw-bold mb-3" style="color: #3d2621;">Project Worksheet</h5>
                        <div class="table-responsive">
                            <table class="table table-sm small align-middle" id="worksheetTable">
                                <thead class="table-light">
                                    <tr><th>Name</th><th>Type</th><th class="text-end">Total</th><th class="text-center">Actions</th></tr>
                                </thead>
                                <tbody id="worksheetRows"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-bold small" style="color: #3d2621;">Worksheet Grand Total:</span>
                            <h3 class="fw-bold m-0" style="color: #3d2621;" id="sheetGrandTotal">P0.00</h3>
                        </div>
                        <form method="POST" action="plan_costs.php?project_id=<?= $project_id ?>">
                            <input type="hidden" name="finalize_sheet" value="1">
                            <input type="hidden" name="cost_items_json" id="costItemsJson">
                            <button type="submit" id="submitSheetButton" class="btn btn-sm w-100 btn-dark py-2 rounded-2 fw-medium">
                                <i class="bi bi-cloud-upload me-1"></i> Save Changes & Finalize
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    const typeSelect = document.getElementById('costTypeSelect');
    const formContainer = document.getElementById('dynamicFormBlocks');
    const totalDisplay = document.getElementById('runningTotalDisplay');
    let worksheetArray = <?= $existing_items_json ?>;

    const subForms = {
        material: `<div class="row g-2"><div class="col-12"><label class="small fw-medium">Material Type</label><input type="text" id="mType" class="form-control form-control-sm" required></div><div class="col-4"><label class="small fw-medium">Unit</label><input type="text" id="mUnit" class="form-control form-control-sm" required></div><div class="col-4"><label class="small fw-medium">Qty</label><input type="number" id="mQty" class="form-control form-control-sm calc" value="1" min="1" required></div><div class="col-4"><label class="small fw-medium">Price</label><input type="number" step="0.01" id="mPrice" class="form-control form-control-sm calc" required></div></div>`,
        furniture: `<div class="row g-2"><div class="col-12"><label class="small fw-medium">Spec Layout</label><input type="text" id="fType" class="form-control form-control-sm" required></div><div class="col-6"><label class="small fw-medium">Qty</label><input type="number" id="fQty" class="form-control form-control-sm calc" value="1" min="1" required></div><div class="col-6"><label class="small fw-medium">Unit Cost</label><input type="number" step="0.01" id="fPrice" class="form-control form-control-sm calc" required></div></div>`,
        labor: `<div class="row g-2"><div class="col-6"><label class="small fw-medium">Wage / Day (P)</label><input type="number" step="0.01" id="lWage" class="form-control form-control-sm calc" required></div><div class="col-6"><label class="small fw-medium">Est. Days</label><input type="number" id="lDays" class="form-control form-control-sm calc" value="1" min="1" required></div></div>`,
        other: `<div class="row g-2"><div class="col-8"><label class="small fw-medium">Notes</label><input type="text" id="oType" class="form-control form-control-sm" required></div><div class="col-4"><label class="small fw-medium">Cost (P)</label><input type="number" step="0.01" id="oCost" class="form-control form-control-sm calc" required></div></div>`
    };

    window.addEventListener('DOMContentLoaded', renderWorksheet);

    typeSelect.addEventListener('change', function() {
        formContainer.innerHTML = subForms[this.value];
        document.querySelectorAll('.calc').forEach(i => i.addEventListener('input', calculateItemTotal));
    });

    function calculateItemTotal() {
        let total = 0;
        const type = typeSelect.value;
        if (type === 'material') total = (parseFloat(document.getElementById('mPrice')?.value) || 0) * (parseInt(document.getElementById('mQty')?.value) || 0);
        else if (type === 'furniture') total = (parseFloat(document.getElementById('fPrice')?.value) || 0) * (parseInt(document.getElementById('fQty')?.value) || 0);
        else if (type === 'labor') total = (parseFloat(document.getElementById('lWage')?.value) || 0) * (parseInt(document.getElementById('lDays')?.value) || 0);
        else if (type === 'other') total = parseFloat(document.getElementById('oCost')?.value) || 0;
        totalDisplay.innerText = 'P' + total.toFixed(2);
        return total;
    }

    function addItemToSheet(e) {
        e.preventDefault();
        const type = typeSelect.value;
        const editIdx = parseInt(document.getElementById('editing_array_index').value);
        let itemObj = { cost_name: document.getElementById('cost_name').value, cost_type: type, calculated_total: calculateItemTotal() };
        
        if (type === 'material') { itemObj.material_type = document.getElementById('mType').value; itemObj.unit = document.getElementById('mUnit').value; itemObj.quantity = document.getElementById('mQty').value; itemObj.price_per_unit = document.getElementById('mPrice').value; }
        else if (type === 'furniture') { itemObj.furniture_type = document.getElementById('fType').value; itemObj.quantity = document.getElementById('fQty').value; itemObj.price = document.getElementById('fPrice').value; }
        else if (type === 'labor') { itemObj.wage_per_day = document.getElementById('lWage').value; itemObj.estimated_days = document.getElementById('lDays').value; }
        else if (type === 'other') { itemObj.other_type = document.getElementById('oType').value; }

        editIdx > -1 ? worksheetArray[editIdx] = itemObj : worksheetArray.push(itemObj);
        renderWorksheet();
        resetFormToNormal();
    }

    function loadItemIntoFormForEditing(index) {
        const item = worksheetArray[index];
        document.getElementById('editing_array_index').value = index;
        document.getElementById('cost_name').value = item.cost_name;
        typeSelect.value = item.cost_type;
        formContainer.innerHTML = subForms[item.cost_type];
        if (item.cost_type === 'material') { document.getElementById('mType').value = item.material_type; document.getElementById('mUnit').value = item.unit; document.getElementById('mQty').value = item.quantity; document.getElementById('mPrice').value = item.price_per_unit; }
        else if (item.cost_type === 'furniture') { document.getElementById('fType').value = item.furniture_type; document.getElementById('fQty').value = item.quantity; document.getElementById('fPrice').value = item.price; }
        else if (item.cost_type === 'labor') { document.getElementById('lWage').value = item.wage_per_day; document.getElementById('lDays').value = item.estimated_days; }
        else if (item.cost_type === 'other') { document.getElementById('oType').value = item.other_type; document.getElementById('oCost').value = item.calculated_total; }
        document.querySelectorAll('.calc').forEach(i => i.addEventListener('input', calculateItemTotal));
        calculateItemTotal();
        document.getElementById('cancelEditBtn').classList.remove('d-none');
    }

    function renderWorksheet() {
        const tbody = document.getElementById('worksheetRows');
        tbody.innerHTML = '';
        let grandTotal = 0;
        worksheetArray.forEach((item, index) => {
            grandTotal += item.calculated_total;
            tbody.innerHTML += `<tr><td class="fw-medium">${item.cost_name}</td><td><span class="badge bg-light border text-dark text-uppercase" style="font-size:0.6rem;">${item.cost_type}</span></td><td class="text-end fw-bold">P${item.calculated_total.toFixed(2)}</td><td class="text-center"><button type="button" onclick="loadItemIntoFormForEditing(${index})" class="btn btn-link py-0 px-1 text-primary"><i class="bi bi-pencil"></i></button><button type="button" onclick="worksheetArray.splice(${index},1);renderWorksheet();" class="btn btn-link py-0 px-1 text-danger"><i class="bi bi-trash"></i></button></td></tr>`;
        });
        document.getElementById('sheetGrandTotal').innerText = 'P' + grandTotal.toFixed(2);
        document.getElementById('costItemsJson').value = JSON.stringify(worksheetArray);
    }

    function resetFormToNormal() {
        document.getElementById('itemDraftForm').reset();
        document.getElementById('editing_array_index').value = "-1";
        formContainer.innerHTML = '';
        totalDisplay.innerText = 'P0.00';
        document.getElementById('cancelEditBtn').classList.add('d-none');
    }
    </script>

    <script>
        window.addEventListener("pageshow", function (event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>
</body>
</html>