document.addEventListener('DOMContentLoaded', function () {
    const form   = document.querySelector('form');
    const btn    = document.querySelector('button[type="submit"]');

    form.addEventListener('submit', function (e) {

        // Basic validation before locking
        const location = document.getElementById('locationSelect').value;
        const checkin  = document.getElementById('checkin').value;
        const checkout = document.getElementById('checkout').value;
        const room     = document.getElementById('roomSelect').value;
        const total    = document.getElementById('totalHidden').value;

        if (!location || !checkin || !checkout || !room || !total) {
            e.preventDefault();
            alert('Please fill in all fields before saving.');
            return;
        }

        // Lock the button on confirmed submit
        btn.disabled    = true;
        btn.textContent = 'Saving...';
    });
});

function fetchAvailableRooms() {
    const location = document.getElementById('locationSelect').value;
    const checkin  = document.getElementById('checkin').value;
    const roomSel  = document.getElementById('roomSelect');
    const roomNote = document.getElementById('roomNote');

    // Update checkout min whenever checkin changes
    if (checkin) {
        const next = new Date(checkin);
        next.setDate(next.getDate() + 1);
        document.getElementById('checkout').min = next.toISOString().split('T')[0];

        const checkout = document.getElementById('checkout').value;
        if (checkout && checkout <= checkin) {
            document.getElementById('checkout').value = '';
        }
    }

    const checkout = document.getElementById('checkout').value;

    // Reset room dropdown
    roomSel.innerHTML    = '<option value="">Loading...</option>';
    roomSel.disabled     = true;
    roomNote.textContent = '';
    roomNote.style.color = '#aaa';
    calcTotal();

    if (!location || !checkin || !checkout) {
        roomSel.innerHTML = '<option value="">Select Branch & Dates First</option>';
        return;
    }

    fetch(`get_available_rooms.php?location=${encodeURIComponent(location)}&checkin=${checkin}&checkout=${checkout}`)
        .then(res => res.json())
        .then(rooms => {
            roomSel.innerHTML = '';

            if (rooms.length === 0) {
                roomSel.innerHTML    = '<option value="">No available rooms for selected dates</option>';
                roomNote.textContent = 'All rooms are booked for this period.';
                roomNote.style.color = '#e53e3e';
                return;
            }

            roomSel.innerHTML = '<option value="">Select a Room</option>';
            rooms.forEach(r => {
                const opt              = document.createElement('option');
                opt.value              = r.RoomID;
                opt.dataset.rate           = r.BaseRate;
                opt.dataset.extraguestrate = r.ExtraGuestRate;
                opt.dataset.maxoccupancies = r.MaxOccupancies;
                opt.textContent        = `${r.RoomType} (${r.RoomNumber}) — ₱${parseFloat(r.BaseRate).toLocaleString('en-PH', {minimumFractionDigits: 2})}/night`;
                roomSel.appendChild(opt);
            });

            roomSel.disabled     = false;
            roomNote.textContent = `${rooms.length} room(s) available`;
            roomNote.style.color = '#4ade80';
        })
        .catch(() => {
            roomSel.innerHTML    = '<option value="">Error loading rooms</option>';
            roomNote.textContent = 'Could not fetch available rooms.';
            roomNote.style.color = '#e53e3e';
        });
}

function calcTotal() {
    const roomSelect  = document.getElementById('roomSelect');
    const checkin     = document.getElementById('checkin').value;
    const checkout    = document.getElementById('checkout').value;
    const selectedOpt = roomSelect.options[roomSelect.selectedIndex];
    const rate        = parseFloat(selectedOpt?.dataset.rate) || 0;
    const extraRate   = parseFloat(selectedOpt?.dataset.extraguestrate) || 0;
    const maxOcc      = parseInt(selectedOpt?.dataset.maxoccupancies) || 1;
    let   guests      = parseInt(document.getElementById('guestsInput').value) || 1;

    document.getElementById('guestsInput').max = maxOcc + 1;
    if (guests > maxOcc + 1) {
        document.getElementById('guestsInput').value = maxOcc + 1;
        guests = maxOcc + 1;
    }

    const extraGuests = Math.max(0, guests - maxOcc);
    const extraFee    = extraGuests * extraRate;

    if (rate && checkin && checkout) {
        const nights = (new Date(checkout) - new Date(checkin)) / (1000 * 60 * 60 * 24);

        if (nights > 0) {
            const weeks     = Math.floor(nights / 7);
            const remaining = nights % 7;
            const discount  = weeks * 7 * rate * 0.10;
            const total     = (weeks * 7 * rate * 0.90) + (remaining * rate) + (extraFee * nights);

            let display = '₱' + total.toLocaleString('en-PH', {minimumFractionDigits: 2});
            if (discount > 0) display += ` (10% off — saved ₱${discount.toLocaleString('en-PH', {minimumFractionDigits: 2})})`;
            if (extraFee > 0) display += ` (+₱${(extraFee * nights).toLocaleString('en-PH', {minimumFractionDigits: 2})} extra guest fee)`;

            document.getElementById('totalDisplay').value = display;
            document.getElementById('totalHidden').value  = total;
            return;
        }
    }

    document.getElementById('totalDisplay').value = '';
    document.getElementById('totalHidden').value  = '';
}