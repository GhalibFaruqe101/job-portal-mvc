<?php
require_once '../helpers/session.php';
require_role('recruiter');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Seekers - JobPortal Recruiter</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/recruiter/recruiter_base.css">
    <link rel="stylesheet" href="../../public/css/recruiter/dashboard.css">
    <link rel="stylesheet" href="../../public/css/recruiter/seekers.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<?php include 'partials/recruiter_nav.php'; ?>

<main class="seekers-main">
    <div class="page-header">
        <div>
            <h1>Candidate Search</h1>
            <p>Find the perfect candidates and reach out to them directly.</p>
        </div>
    </div>

    <!-- Search Form -->
    <div class="search-form-card">
        <form id="seekerSearchForm" class="search-grid">
            <div class="form-group">
                <label for="keyword">Keywords (Skills, Title, Name)</label>
                <input type="text" id="keyword" class="form-control" placeholder="e.g. Laravel, React, Manager">
            </div>
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" class="form-control" placeholder="e.g. Dhaka">
            </div>
            <div class="form-group">
                <label for="exp_min">Min Experience (Yrs)</label>
                <input type="number" id="exp_min" class="form-control" placeholder="0" min="0">
            </div>
            <div class="form-group">
                <label for="salary_max">Max Expected Salary (৳)</label>
                <input type="number" id="salary_max" class="form-control" placeholder="e.g. 80000" min="0">
            </div>
            <div class="form-group" style="display: flex; align-items: flex-end;">
                <button type="submit" class="btn-search">Search Candidates</button>
            </div>
        </form>
    </div>

    <div id="loading" style="display:none; text-align:center; padding: 3rem; color: #64748b;">⏳ Searching database...</div>

    <div id="results-grid" class="seekers-grid">
        <div class="empty-state">
            <div class="empty-icon">🔍</div>
            <p>Enter your criteria above and click Search.</p>
        </div>
    </div>
</main>

<script>
document.getElementById('seekerSearchForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const kw = document.getElementById('keyword').value.trim();
    const loc = document.getElementById('location').value.trim();
    const exp = document.getElementById('exp_min').value.trim();
    const sal = document.getElementById('salary_max').value.trim();

    const resultsGrid = document.getElementById('results-grid');
    const loading = document.getElementById('loading');

    resultsGrid.innerHTML = '';
    loading.style.display = 'block';

    const params = new URLSearchParams();
    if(kw) params.append('keyword', kw);
    if(loc) params.append('location', loc);
    if(exp) params.append('exp_min', exp);
    if(sal) params.append('salary_max', sal);

    fetch('../api/search_seekers.php?' + params.toString())
        .then(res => res.json())
        .then(data => {
            loading.style.display = 'none';

            if(data.length === 0) {
                resultsGrid.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-icon">😕</div>
                        <p>No candidates found matching your criteria.</p>
                    </div>`;
                return;
            }

            const escapeHtml = (unsafe) => {
                return (unsafe || '').toString()
                     .replace(/&/g, "&amp;")
                     .replace(/</g, "&lt;")
                     .replace(/>/g, "&gt;")
                     .replace(/"/g, "&quot;")
                     .replace(/'/g, "&#039;");
            };

            resultsGrid.innerHTML = data.map(s => `
                <div class="seeker-card">
                    <div class="seeker-header">
                        <div class="seeker-avatar">${escapeHtml(s.name.charAt(0).toUpperCase())}</div>
                        <div class="seeker-title">
                            <h2>${escapeHtml(s.name)}</h2>
                            <p>${escapeHtml(s.headline)}</p>
                        </div>
                    </div>
                    <div class="seeker-meta">
                        <span>📍 ${escapeHtml(s.preferred_location)}</span>
                        <span>📊 ${escapeHtml(s.years_experience)} yrs exp</span>
                        <span>💰 ৳${escapeHtml(s.expected_salary)}</span>
                    </div>
                    <div class="seeker-skills">
                        ${(s.skills || '').split(',').map(skill => skill.trim() ? `<span class="skill-tag">${escapeHtml(skill.trim())}</span>` : '').join('')}
                    </div>
                    <div class="seeker-actions">
                        <a href="seeker_profile.php?id=${encodeURIComponent(s.id)}" class="btn-view-profile">View Profile</a>
                    </div>
                </div>
            `).join('');
        })
        .catch(err => {
            loading.style.display = 'none';
            resultsGrid.innerHTML = `<div class="empty-state"><p>Error occurred while searching.</p></div>`;
        });
});

// Auto-run search on page load so recruiters can see the latest seekers live immediately
document.getElementById('seekerSearchForm').dispatchEvent(new Event('submit', { cancelable: true }));
</script>

</body>
</html>


