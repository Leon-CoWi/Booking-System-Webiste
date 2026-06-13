// filter bookings by branch
function filterBranch(branch, el) {
    document.querySelectorAll('.branch-btn').forEach(b => b.classList.remove('active-btn'));
    el.classList.add('active-btn');

    document.querySelectorAll('.res-row').forEach(row => {
        row.style.display = (branch === 'all' || row.classList.contains(branch)) ? '' : 'none';
    });
}

// accept/reject/done confirmation handlers
document.querySelectorAll('a[href*="accept="]').forEach(link => {
    link.addEventListener('click', function (e) {
        e.preventDefault();
        if (confirm('Accept this booking?')) {
            this.querySelector('button').disabled = true;
            this.querySelector('button').textContent = 'Accepting...';
            window.location.href = this.href;
        }
    });
});

document.querySelectorAll('a[href*="reject="]').forEach(link => {
    link.addEventListener('click', function (e) {
        e.preventDefault();
        if (confirm('Reject this booking?')) {
            this.querySelector('button').disabled = true;
            this.querySelector('button').textContent = 'Rejecting...';
            window.location.href = this.href;
        }
    });
});

document.querySelectorAll('a[href*="done="]').forEach(link => {
    link.addEventListener('click', function (e) {
        e.preventDefault();
        if (confirm('Mark this booking as done?')) {
            this.querySelector('button').disabled = true;
            this.querySelector('button').textContent = 'Processing...';
            window.location.href = this.href;
        }
    });
});

// current state
let currentBookingID  = null;
let currentRoomData   = [];

// close modal
function closeModal(id) {
    document.getElementById(id).classList.remove('open');
}

// format date
function formatDate(dateStr) {
    if (!dateStr) return '—';
    const d = new Date(dateStr + 'T00:00:00');
    return d.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
}

// format peso
function formatPeso(amount) {
    if (!amount) return '—';
    return '₱' + parseFloat(amount).toLocaleString('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

// open edit modal
function openEditModal(bookingID, checkIn, checkOut, roomID, guests) {
    currentBookingID = bookingID;
    currentRoomData  = [];

    document.getElementById('editModalTitle').textContent = '#' + bookingID;
    document.getElementById('editCheckIn').value  = checkIn;
    document.getElementById('editCheckOut').value = checkOut;
    document.getElementById('editGuests').value   = guests;

    // reset preview
    ['previewNights','previewRate','previewExtraFee','previewDiscount','previewTotal']
        .forEach(id => document.getElementById(id).textContent = '—');

    resetSaveBtn();
    document.getElementById('editModal').classList.add('open');

    loadAvailableRooms(roomID);
}

// load available rooms
async function loadAvailableRooms(preselectRoomID = null) {
    const checkIn  = document.getElementById('editCheckIn').value;
    const checkOut = document.getElementById('editCheckOut').value;
    const select   = document.getElementById('editRoomID');

    if (!checkIn || !checkOut || checkIn >= checkOut) {
        select.innerHTML = '<option value="">— set valid dates first —</option>';
        currentRoomData = [];
        updateEditPreview();
        return;
    }

    select.innerHTML = '<option value="">Loading...</option>';
    select.disabled  = true;

    try {
        const res = await fetch(
            `reservations.php?get_rooms=1&booking_id=${currentBookingID}&check_in=${checkIn}&check_out=${checkOut}`
        );

        currentRoomData = await res.json();

        select.innerHTML = '';

        if (currentRoomData.length === 0) {
            select.innerHTML = '<option value="">No available rooms</option>';
        } else {
            currentRoomData.forEach(r => {
                const opt = document.createElement('option');
                opt.value = r.RoomID;
                opt.textContent =
                    `${r.RoomID} — ${r.RoomType} (${r.RoomNumber}) · ₱${parseFloat(r.BaseRate).toLocaleString('en-PH', { minimumFractionDigits: 2 })}/night · Max ${r.MaxOccupancies} guests`;

                opt.dataset.baseRate       = r.BaseRate;
                opt.dataset.extraGuestRate = r.ExtraGuestRate ?? 400;
                opt.dataset.maxOccupancies = r.MaxOccupancies ?? 1;

                if (preselectRoomID && r.RoomID === preselectRoomID) opt.selected = true;

                select.appendChild(opt);
            });
        }
    } catch (e) {
        select.innerHTML = '<option value="">Error loading rooms</option>';
        currentRoomData = [];
    }

    select.disabled = false;
    updateEditPreview();
}

// update price preview
function updateEditPreview() {
    const checkIn  = document.getElementById('editCheckIn').value;
    const checkOut = document.getElementById('editCheckOut').value;
    const guests   = parseInt(document.getElementById('editGuests').value) || 1;
    const select   = document.getElementById('editRoomID');
    const opt      = select.options[select.selectedIndex];

    if (!checkIn || !checkOut || checkIn >= checkOut || !opt || !opt.value) {
        ['previewNights','previewRate','previewExtraFee','previewDiscount','previewTotal']
            .forEach(id => document.getElementById(id).textContent = '—');
        return;
    }

    const nights = Math.round((new Date(checkOut) - new Date(checkIn)) / 86400000);

    const baseRate       = parseFloat(opt.dataset.baseRate) || 0;
    const extraGuestRate = parseFloat(opt.dataset.extraGuestRate) || 0;
    const maxOccupancies = parseInt(opt.dataset.maxOccupancies) || 1;

    document.getElementById('editGuests').max = maxOccupancies + 10;

    const extraGuests   = Math.max(0, guests - maxOccupancies);
    const extraGuestFee = extraGuests * extraGuestRate * nights;

    const discountedWeeks = Math.floor(nights / 7);
    const remainingDays   = nights % 7;

    const discountAmount = (discountedWeeks * 7 * baseRate) * 0.10;

    const total =
        (discountedWeeks * 7 * baseRate * 0.90) +
        (remainingDays * baseRate) +
        extraGuestFee;

    document.getElementById('previewNights').textContent   = nights + ' night' + (nights !== 1 ? 's' : '');
    document.getElementById('previewRate').textContent     = formatPeso(baseRate);
    document.getElementById('previewExtraFee').textContent  =
        extraGuests > 0
            ? `${formatPeso(extraGuestFee)} (${extraGuests} extra guest${extraGuests > 1 ? 's' : ''})`
            : '₱0.00';

    document.getElementById('previewDiscount').textContent =
        discountedWeeks > 0
            ? `-${formatPeso(discountAmount)} (${discountedWeeks} week${discountedWeeks > 1 ? 's' : ''})`
            : '₱0.00';

    document.getElementById('previewTotal').textContent = formatPeso(total);
}

// save edit booking
async function saveEdit() {
    const checkIn  = document.getElementById('editCheckIn').value;
    const checkOut = document.getElementById('editCheckOut').value;
    const roomID   = document.getElementById('editRoomID').value;
    const guests   = parseInt(document.getElementById('editGuests').value) || 1;
    const saveBtn  = document.getElementById('editSaveBtn');

    if (!checkIn || !checkOut || !roomID) return alert('Fill all fields');
    if (checkIn >= checkOut) return alert('Invalid dates');
    if (guests < 1) return alert('Invalid guests');

    saveBtn.textContent = 'Saving...';
    saveBtn.classList.add('btn-saving');

    const body = new FormData();
    body.append('action', 'edit_booking');
    body.append('booking_id', currentBookingID);
    body.append('check_in', checkIn);
    body.append('check_out', checkOut);
    body.append('room_id', roomID);
    body.append('guests', guests);

    try {
        const res = await fetch('reservations.php', { method: 'POST', body });
        const data = await res.json();

        if (data.success) {
            closeModal('editModal');
            location.reload();
        } else {
            alert('Error saving');
            resetSaveBtn();
        }
    } catch (e) {
        alert('Network error');
        resetSaveBtn();
    }
}

// reset save button
function resetSaveBtn() {
    const btn = document.getElementById('editSaveBtn');
    btn.textContent = 'Save Changes';
    btn.classList.remove('btn-saving');
}

// cancel booking
async function cancelBooking() {
    if (!confirm('Cancel this booking?')) return;

    const cancelBtn = document.getElementById('editCancelBtn');
    cancelBtn.textContent = 'Cancelling...';
    cancelBtn.classList.add('btn-saving');

    const body = new FormData();
    body.append('action', 'cancel_booking');
    body.append('booking_id', currentBookingID);

    try {
        const res = await fetch('reservations.php', { method: 'POST', body });
        const data = await res.json();

        if (data.success) {
            closeModal('editModal');
            location.reload();
        } else {
            alert('Error');
            cancelBtn.textContent = 'Cancel Booking';
            cancelBtn.classList.remove('btn-saving');
        }
    } catch (e) {
        alert('Network error');
    }
}

// open view modal
async function openViewModal(bookingID) {
    document.getElementById('viewModalTitle').textContent = '#' + bookingID;

    const fields = [
        'vCustomerName','vEmail','vPhone','vLocation','vRoom','vRoomType',
        'vCheckIn','vCheckOut','vNights','vGuests',
        'vPaymentID','vPayStatus','vMethod','vPayDate',
        'vCharges','vDiscount','vTotal','vPaid'
    ];

    fields.forEach(id => document.getElementById(id).textContent = '…');

    document.getElementById('viewModal').classList.add('open');

    try {
        const res = await fetch(`reservations.php?get_booking=1&booking_id=${bookingID}`);
        const d = await res.json();

        const img = document.getElementById("vReceipt");

        if (d.ReceiptPath) {
            img.src = "../User/" + d.ReceiptPath;
            img.style.display = "block";
        } else {
            img.style.display = "none";
        }

        document.getElementById('vCustomerName').textContent = d.CustomerName || '—';
        document.getElementById('vEmail').textContent        = d.Email || '—';
        document.getElementById('vPhone').textContent        = d.PhoneNumber || '—';
        document.getElementById('vLocation').textContent     = d.Location || '—';
        document.getElementById('vRoom').textContent         = d.RoomID || '—';
        document.getElementById('vRoomType').textContent     = d.RoomType || '—';
        document.getElementById('vCheckIn').textContent      = formatDate(d.CheckInDate);
        document.getElementById('vCheckOut').textContent     = formatDate(d.CheckOutDate);
        document.getElementById('vNights').textContent       = d.Nights || '—';
        document.getElementById('vGuests').textContent       = d.GuestNumber || '—';

        document.getElementById('vPaymentID').textContent    = '#' + d.PaymentID;
        document.getElementById('vPayStatus').textContent    = d.PaymentStatus || '—';
        document.getElementById('vMethod').textContent       = d.PaymentMethod || '—';
        document.getElementById('vPayDate').textContent      = formatDate(d.PaymentDate);
        document.getElementById('vCharges').textContent      = formatPeso(d.Charges);
        document.getElementById('vDiscount').textContent     = formatPeso(d.DiscountAmount);
        document.getElementById('vTotal').textContent        = formatPeso(d.TotalAmount);
        document.getElementById('vPaid').textContent         = formatPeso(d.PaidAmount);

    } catch (e) {
        alert('Could not load booking');
        closeModal('viewModal');
    }
}

// close modal on backdrop click
document.querySelectorAll('.modal-backdrop').forEach(el => {
    el.addEventListener('click', function (e) {
        if (e.target === this) closeModal(this.id);
    });
});