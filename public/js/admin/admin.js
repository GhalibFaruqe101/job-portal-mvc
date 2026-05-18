

(function () {
    function csrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function postForm(url, data, callback) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onreadystatechange = function () {
            if (xhr.readyState !== 4) return;
            var response;
            try {
                response = JSON.parse(xhr.responseText);
            } catch (e) {
                response = { ok: false, message: 'Invalid JSON response from server.' };
            }
            callback(response, xhr.status);
        };
        var encoded = Object.keys(data).map(function (key) {
            return encodeURIComponent(key) + '=' + encodeURIComponent(data[key]);
        }).join('&');
        xhr.send(encoded);
    }

    function showToast(message, ok) {
        var existing = document.querySelector('.admin-toast');
        if (existing) existing.remove();
        var toast = document.createElement('div');
        toast.className = 'admin-toast ' + (ok ? 'success' : 'error');
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(function () { toast.remove(); }, 3500);
    }

    document.addEventListener('click', function (event) {
        var accountBtn = event.target.closest('.ajax-account-action');
        if (accountBtn) {
            var action = accountBtn.dataset.action;
            var needsReason = accountBtn.dataset.needsReason === '1';
            var reason = '';
            if (needsReason) {
                reason = prompt('Please write the reason for this action:') || '';
                if (reason.trim() === '') {
                    showToast('Reason is required for this action.', false);
                    return;
                }
            } else if (!confirm('Confirm this account action?')) {
                return;
            }

            accountBtn.disabled = true;
            postForm('api/account_action.php', {
                csrf_token: csrfToken(),
                user_id: accountBtn.dataset.userId,
                account_action: action,
                reason: reason
            }, function (res) {
                accountBtn.disabled = false;
                showToast(res.message || 'Action completed.', !!res.ok);
                if (res.ok) {
                    setTimeout(function () { window.location.reload(); }, 650);
                }
            });
            return;
        }

        var featuredBtn = event.target.closest('.ajax-featured-toggle');
        if (featuredBtn) {
            featuredBtn.disabled = true;
            postForm('api/featured_toggle.php', {
                csrf_token: csrfToken(),
                job_id: featuredBtn.dataset.jobId
            }, function (res) {
                featuredBtn.disabled = false;
                showToast(res.message || 'Featured status updated.', !!res.ok);
                if (res.ok) {
                    featuredBtn.textContent = parseInt(res.is_featured, 10) === 1 ? 'Featured' : 'Set Featured';
                    if (window.location.href.indexOf('action=featured') !== -1 && parseInt(res.is_featured, 10) === 0) {
                        var row = featuredBtn.closest('[data-job-row]');
                        if (row) row.remove();
                    }
                }
            });
            return;
        }

        var complaintBtn = event.target.closest('.ajax-complaint-resolve');
        if (complaintBtn) {
            var card = complaintBtn.closest('[data-complaint-row]');
            var noteBox = card ? card.querySelector('.complaint-note') : null;
            var note = noteBox ? noteBox.value.trim() : '';
            if (note === '') {
                showToast('Resolution note is required.', false);
                return;
            }
            complaintBtn.disabled = true;
            postForm('api/complaint_resolve.php', {
                csrf_token: csrfToken(),
                complaint_id: complaintBtn.dataset.complaintId,
                admin_note: note
            }, function (res) {
                complaintBtn.disabled = false;
                showToast(res.message || 'Complaint updated.', !!res.ok);
                if (res.ok) {
                    setTimeout(function () { window.location.reload(); }, 650);
                }
            });
        }
    });
})();
