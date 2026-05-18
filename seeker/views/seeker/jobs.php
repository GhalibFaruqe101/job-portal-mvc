<?php

$pageTitle = 'Find Jobs';
$activeNav = 'jobs';
require __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <h1>Find Jobs</h1>

    <!-- Search & Filters -->
    <div class="search-bar">
        <input type="text" id="search-q" placeholder="Job title, keyword, or company…"
               value="<?= htmlspecialchars($filters['keyword']) ?>">
        <button onclick="doSearch()" class="btn">Search</button>
    </div>

    <div class="filter-row" id="filter-bar">
        <select id="f-category">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= (int)$cat['id'] ?>"
                    <?= $filters['catId'] == $cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <input type="text" id="f-location" placeholder="Location"
               value="<?= htmlspecialchars($filters['location']) ?>">

        <select id="f-job-type">
            <option value="">All Types</option>
            <?php foreach (['full-time','part-time','remote','contract'] as $t): ?>
                <option value="<?= $t ?>" <?= $filters['jobType'] === $t ? 'selected' : '' ?>>
                    <?= ucfirst($t) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select id="f-exp-level">
            <option value="">All Levels</option>
            <?php foreach (['entry','mid','senior'] as $lv): ?>
                <option value="<?= $lv ?>" <?= $filters['expLevel'] === $lv ? 'selected' : '' ?>>
                    <?= ucfirst($lv) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <input type="number" id="f-sal-min" placeholder="Salary min" min="0"
               value="<?= (int)$filters['salMin'] ?>">
        <input type="number" id="f-sal-max" placeholder="Salary max" min="0"
               value="<?= (int)$filters['salMax'] ?>">
    </div>

    <!-- Results count -->
    <p class="results-count" id="results-count">
        Showing <strong><?= count($jobs) ?></strong> jobs
    </p>

    <!-- Job Cards Container  -->
    <div id="jobs-container">
        <?php foreach ($jobs as $job): ?>
            <?php include __DIR__ . '/partials/job_card.php'; ?>
        <?php endforeach; ?>
        <?php if (empty($jobs)): ?>
            <p class="muted">No jobs match your search. Try different filters.</p>
        <?php endif; ?>
    </div>
</div>

<script>

let debounceTimer;

function doSearch() {
    const q        = document.getElementById('search-q').value;
    const category = document.getElementById('f-category').value;
    const location = document.getElementById('f-location').value;
    const jobType  = document.getElementById('f-job-type').value;
    const expLevel = document.getElementById('f-exp-level').value;
    const salMin   = document.getElementById('f-sal-min').value;
    const salMax   = document.getElementById('f-sal-max').value;

    const params = new URLSearchParams({ q, category, location,
        job_type: jobType, exp_level: expLevel, sal_min: salMin, sal_max: salMax });

    const xhr = new XMLHttpRequest();
    xhr.open('GET', '/job_portal/seeker/api/jobs_search.php?' + params.toString(), true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

    xhr.onreadystatechange = function () {
        if (xhr.readyState !== 4) return;
        if (xhr.status === 200) {
            const data = JSON.parse(xhr.responseText);
            renderJobs(data.jobs);
            document.getElementById('results-count').innerHTML =
                'Showing <strong>' + data.count + '</strong> jobs';
        } else {
            document.getElementById('jobs-container').innerHTML =
                '<p class="error">Failed to load results. Please try again.</p>';
        }
    };
    xhr.send();
}

function renderJobs(jobs) {
    const container = document.getElementById('jobs-container');
    if (jobs.length === 0) {
        container.innerHTML = '<p class="muted">No jobs match your search.</p>';
        return;
    }
    container.innerHTML = jobs.map(j => `
        <div class="job-card ${j.is_featured ? 'featured' : ''}">
            <div class="job-card-top">
                ${j.logo_path ? `<img src="${j.logo_path}" class="company-logo" alt="">` : ''}
                <div>
                    <a href="/job_portal/seeker/index.php?action=jobDetail&id=${j.id}" class="job-title">${escHtml(j.title)}</a>
                    <p class="job-company">${escHtml(j.company_name)}${j.agency_name ? ' via ' + escHtml(j.agency_name) : ''}</p>
                    <div class="badge-row">
                        <span class="badge">${escHtml(j.job_type)}</span>
                        <span class="badge">${escHtml(j.experience_level)}</span>
                        ${j.salary_min ? `<span class="badge salary">৳ ${Number(j.salary_min).toLocaleString()}–${Number(j.salary_max).toLocaleString()}</span>` : ''}
                        ${j.is_featured ? '<span class="badge featured">Featured</span>' : ''}
                    </div>
                </div>
            </div>
            <div class="job-card-footer">
                <span class="muted"> ${escHtml(j.location)}</span>
                <span class="muted">Deadline: ${j.deadline ?? '—'}</span>
                <a href="/job_portal/seeker/index.php?action=jobDetail&id=${j.id}" class="btn-sm">View &amp; Apply</a>
            </div>
        </div>
    `).join('');
}

function escHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}


['f-category','f-job-type','f-exp-level'].forEach(id => {
    document.getElementById(id).addEventListener('change', doSearch);
});
['f-location','f-sal-min','f-sal-max','search-q'].forEach(id => {
    document.getElementById(id).addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(doSearch, 400);
    });
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>