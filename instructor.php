<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Instructor Portal — EasyPrimeTech Scholarship</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles/instructor.css">
    <!-- ── Cache Buster: Service Worker Registration ───────────────────────────────────────
        HOW TO FORCE A REFRESH: Bump the version number in version.json
        e.g. "1.0.0" → "1.0.1" and every browser will clear cache & reload. ─── -->
    <script>
      (async function() {
        if (!("serviceWorker" in navigator)) return;
        const reg = await navigator.serviceWorker.register("/sw.js", { updateViaCache: "none" });
        reg.update().catch(() => {});
        try {
          const res  = await fetch("/version.json?t=" + Date.now(), { cache: "no-store" });
          const data = await res.json();
          const newV = data.version;
          const oldV = localStorage.getItem("ept_version");
          if (oldV && oldV !== newV) {
            localStorage.setItem("ept_version", newV);
            if (reg.active) reg.active.postMessage({ type: "CLEAR_CACHE_AND_RELOAD", version: newV });
            window.location.reload(true);
          } else {
            localStorage.setItem("ept_version", newV);
          }
        } catch(e) { console.warn("Version check failed:", e.message); }
        navigator.serviceWorker.addEventListener("message", e => {
          if (e.data && e.data.type === "RELOAD") window.location.reload(true);
        });
      })();
    </script>
  </head>
  <body>

    <!-- LOGIN -->
    <div class="login-overlay" id="loginOverlay">
      <div class="login-box">
        <div class="login-logo">Easy<span>Prime</span>Tech</div>
        <div class="login-tag">Instructor Portal</div>
        <form action="scripts/instructor.php" method="post">
          <h2>Sign In</h2>
          <p>Enter your instructor credentials to access your students.</p>
          <div class="login-field">
            <label>Email</label>
            <input name="ins_email" type="email" id="loginEmail" placeholder="instructor@easyprime.tech" autocomplete="email">
          </div>
          <div class="login-field">
            <label>Password</label>
            <input name="ins_pw" type="password" id="loginPass" placeholder="••••••••">
          </div>
          <button class="login-btn" id="loginBtn" onclick="doLogin()"><i class="fas fa-lock"></i> Sign In</button>
          <div class="login-err" id="loginErr"></div>
        </form>
      </div>
    </div>

    <!-- SIDEBAR OVERLAY -->
    <div class="sb-overlay" id="sbOverlay" onclick="closeSidebar()"></div>

    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">
      <div class="sb-logo"><h3>Easy<span>Prime</span>Tech</h3><small>Instructor Portal</small></div>
      <div class="sb-badge" id="sbBadge" style="display:none">
        <div class="sb-badge-name" id="sbName">—</div>
        <div class="sb-badge-sub" id="sbGroup">—</div>
        <div class="sb-badge-count" id="sbCount">0 students</div>
      </div>
      <div class="sb-section">
        <div class="sb-label">Manage</div>
        <button class="sb-item active" onclick="showSection('sessions',this)"><i class="fas fa-broadcast-tower"></i> Class Sessions</button>
        <button class="sb-item" onclick="showSection('attendance',this)"><i class="fas fa-calendar-check"></i> Attendance Logs</button>
        <button class="sb-item" onclick="showSection('scores',this)"><i class="fas fa-star"></i> Update Scores</button>
        <button class="sb-item" onclick="showSection('scholars',this)"><i class="fas fa-users"></i> My Students</button>
        <button class="sb-item" onclick="showSection('activity',this)"><i class="fas fa-comments"></i> Activity & Q&A</button>
      </div>
      <div class="sb-divider"></div>
      <div class="sb-section">
        <div class="sb-label">Links</div>
        <button class="sb-item" onclick="window.open('index.html','_blank')"><i class="fas fa-home"></i> View Site</button>
      </div>
      <div class="sb-bottom"><button class="logout-btn" onclick="doLogout()"><i class="fas fa-sign-out-alt"></i> Sign Out</button></div>
    </div>

    <!-- MAIN -->
    <div class="main" id="mainPanel" style="display:none">
      <div class="topbar">
        <div style="display:flex;align-items:center;gap:12px">
          <button class="menu-toggle" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
          <h1 id="topbarTitle">Class Sessions</h1>
        </div>
        <div style="display:flex;align-items:center;gap:10px">
          <button class="tb-btn btn-refresh" onclick="refreshCurrent()"><i class="fas fa-sync"></i> <span>Refresh</span></button>
        </div>
      </div>

      <div class="skill-banner" id="skillBanner">
        <i class="fas fa-chalkboard-teacher" style="font-size:18px;opacity:.8"></i>
        <div>
          <div style="font-size:11px;text-transform:uppercase;letter-spacing:1px;opacity:.6;font-weight:700">Your Teaching Area</div>
          <div class="skill-name" id="skillBannerName">—</div>
        </div>
      </div>

      <div class="dash-body">

        <!-- ═══════════════════ SESSIONS ═══════════════════════ -->
        <div class="section-panel active" id="sec-sessions">
          <div class="stats-bar">
            <div class="stat-card c-blue"><div class="stat-lbl">My Students</div><div class="stat-val" id="sSessions">—</div></div>
            <div class="stat-card c-green"><div class="stat-lbl">Sessions Created</div><div class="stat-val" id="sAttendances">—</div></div>
            <div class="stat-card c-orange"><div class="stat-lbl">Total Attendances</div><div class="stat-val" id="sActive">—</div></div>
            <div class="stat-card c-gold"><div class="stat-lbl">Active Sessions</div><div class="stat-val" id="sScholars">—</div></div>
          </div>

          <div class="card">
            <div class="card-hd">
              <div>
                <div class="card-title">Create New Session</div>
                <div class="card-sub">Generate a 6-character code. Students who don't use it before expiry are automatically marked <strong>Absent</strong>.</div>
              </div>
            </div>
            <div class="form-row">
              <div class="fg"><label class="lbl">Session Name *</label><input class="inp" id="sessionName" placeholder="e.g. Week 3 — Class 1"></div>
              <div class="fg"><label class="lbl">Today's Topic *</label><input class="inp" id="sessionTopic" placeholder="e.g. Functions and Loops"></div>
              <div class="fg">
                <label class="lbl">Week Number</label>
                <select class="inp" id="sessionWeek">
                  <option value="1">Week 1</option><option value="2">Week 2</option><option value="3">Week 3</option>
                  <option value="4">Week 4</option><option value="5">Week 5</option><option value="6">Week 6</option>
                  <option value="7">Week 7</option><option value="8">Week 8</option>
                </select>
              </div>
              <div class="fg">
                <label class="lbl">Class Number</label>
                <select class="inp" id="sessionClass">
                  <option value="1">Class 1</option><option value="2">Class 2</option>
                </select>
              </div>
            </div>
            <div style="background:#fffbeb;border:1px solid #f59e0b;border-radius:var(--radius);padding:10px 14px;font-size:12px;color:#92400e;margin-bottom:14px;line-height:1.5">
              <i class="fas fa-info-circle"></i> Code expires in <strong> 30 minutes</strong>. Absent students are automatically recorded when the code expires. Attendance score (+2.5%) is added to the student's record immediately when they mark attendance.
            </div>
            <button class="btn btn-blue" onclick="createSession()" id="createSessionBtn">
              <i class="fas fa-plus"></i> Create Session &amp; Generate Code
            </button>
          </div>

          <!-- Active session card -->
          <div class="card" id="activeSessionCard" style="display:none;border-top:3px solid var(--green)">
            <div class="card-hd">
              <div>
                <div class="card-title">🔴 Live Session</div>
                <div class="card-sub" id="activeSessionMeta">—</div>
              </div>
              <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                <span class="live-badge">Live</span>
                <button class="btn btn-danger" onclick="closeSession()"><i class="fas fa-stop-circle"></i> Close Session</button>
              </div>
            </div>
            <p style="font-size:13px;color:var(--muted);margin-bottom:2px">Share this code with your students — tap to copy</p>
            <div class="code-display" onclick="copyCode()">
              <span class="code-big" id="activeCodeDisplay">——</span>
              <span class="code-hint"><i class="fas fa-copy"></i> Click to copy</span>
            </div>
            <div style="text-align:center;margin-top:8px;font-size:14px;color:var(--muted)">
              <i class="fas fa-users" style="color:var(--primary)"></i>
              <span id="attendeeCount">0 students marked attendance</span>
            </div>
            <div style="text-align:center;margin-top:6px">
              <span id="sessionCountdown" style="display:none;font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:var(--green)"></span>
            </div>
          </div>

          <div class="card">
            <div class="card-hd"><div class="card-title">My Session History</div></div>
            <div class="tbl-wrap">
              <table>
                <thead><tr><th>Session</th><th>Week/Class</th><th>Date</th><th>Code</th><th>Attendees</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody id="pastSessionsBody"><tr><td class="empty-row" colspan="7">No sessions yet. Create one above.</td></tr></tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- ═══════════════════ ATTENDANCE ═══════════════════════ -->
        <div class="section-panel" id="sec-attendance">

          <!-- Per-student weekly breakdown -->
          <div class="card">
            <div class="card-hd">
              <div>
                <div class="card-title">Student Attendance Records</div>
                <div class="card-sub">Full 8-week breakdown per student. Delete any record to reset it to Absent (0 score).</div>
              </div>
              <button class="btn btn-blue" style="font-size:12px;padding:8px 14px" onclick="loadAttendanceLogs()"><i class="fas fa-sync"></i> Refresh</button>
            </div>
            <div style="display:flex;gap:10px;margin-bottom:16px;flex-wrap:wrap">
              <input class="inp" type="text" id="attFilterName" placeholder="Filter by name or Scholar ID…" oninput="renderAttendanceView()" style="flex:1;min-width:180px">
              <select class="inp" id="attFilterWeek" onchange="renderAttendanceView()" style="width:130px">
                <option value="">All Weeks</option>
                <option value="1">Week 1</option><option value="2">Week 2</option><option value="3">Week 3</option>
                <option value="4">Week 4</option><option value="5">Week 5</option><option value="6">Week 6</option>
                <option value="7">Week 7</option><option value="8">Week 8</option>
              </select>
            </div>
            <div id="attendanceView">
              <div style="text-align:center;padding:32px;color:var(--muted)">Click Refresh to load attendance records.</div>
            </div>
          </div>

          <!-- Flat log -->
          <div class="card">
            <div class="card-hd">
              <div>
                <div class="card-title">Full Attendance Log</div>
                <div class="card-sub">Every attendance event across all sessions</div>
              </div>
            </div>
            <div style="margin-bottom:14px">
              <label class="lbl">Filter by Session</label>
              <select class="inp" id="sessionFilter" onchange="loadAttendanceLogs()" style="max-width:320px">
                <option value="">All sessions</option>
              </select>
            </div>
            <div class="tbl-wrap">
              <table>
                <thead><tr><th>Name</th><th>Scholar ID</th><th>Session</th><th>Status</th><th>Score Added</th><th>Date & Time</th><th></th></tr></thead>
                <tbody id="attLogsBody"><tr><td class="empty-row" colspan="7">Click Refresh above to load records.</td></tr></tbody>
              </table>
            </div>
            <div id="attSummary" style="display:none;margin-top:14px;padding:14px;background:#f8f9ff;border:1px solid var(--border);border-radius:8px;font-size:14px;color:var(--muted)"></div>
          </div>
        </div>

        <!-- ═══════════════════ SCORES ═══════════════════════ -->
        <div class="section-panel" id="sec-scores">
          <div class="access-denied" id="accessDenied">
            <i class="fas fa-lock"></i>
            <div>This scholar is not assigned to you. You can only update scores for students in your assigned group.</div>
          </div>

          <div class="card">
            <div class="card-hd">
              <div>
                <div class="card-title">Find Your Student</div>
                <div class="card-sub">Enter a Scholar ID to view and edit their scores</div>
              </div>
            </div>
            <div style="display:flex;gap:10px">
              <input class="inp" type="text" id="scoreSearchId" placeholder="Scholar ID e.g. mane_25" style="flex:1" oninput="this.value=this.value.toLowerCase().replace(/[^a-z0-9_]/g,'')">
              <button class="btn btn-blue" onclick="findScholar()"><i class="fas fa-search"></i> Find Student</button>
            </div>
            <div class="alert alert-error" id="scholarNotFound"><i class="fas fa-exclamation-circle"></i> Scholar not found or not assigned to you.</div>
          </div>

          <div id="scoreEditor" style="display:none">

            <!-- Student header + summary -->
            <div class="card" style="border-top:3px solid var(--primary)">
              <div class="card-hd">
                <div>
                  <div class="card-title" id="seScholarName">—</div>
                  <div class="card-sub" id="seScholarId">—</div>
                </div>
                <span class="badge badge-found">✓ Assigned to You</span>
              </div>
              <div class="score-summary" id="scoreSummary">
                <div class="ssb"><div class="ssb-val" id="ss-att">—</div><div class="ssb-lbl">Attendance</div></div>
                <div class="ssb"><div class="ssb-val" id="ss-proj">—</div><div class="ssb-lbl">Projects</div></div>
                <div class="ssb"><div class="ssb-val" id="ss-quiz">—</div><div class="ssb-lbl">Quizzes</div></div>
                <div class="ssb"><div class="ssb-val" id="ss-bonus">—</div><div class="ssb-lbl">Bonus</div></div>
              </div>
            </div>

            <!-- PROJECTS — 4 individual cards -->
            <div class="card">
              <div class="card-hd">
                <div>
                  <div class="card-title"><i class="fas fa-project-diagram" style="color:var(--primary);margin-right:8px"></i>Project Scores</div>
                  <div class="card-sub">4 projects, each out of 10 marks. Total = 40 marks (30% of final grade).</div>
                </div>
              </div>
              <div class="project-grid" id="projectGrid"><!-- rendered by JS --></div>
              <div class="totals-bar">
                <strong>Total:</strong> <span id="projTotalDisp" style="font-family:'Syne',sans-serif;font-weight:800;color:var(--primary)">0 / 40</span>
                &nbsp;|&nbsp; <strong>Avg:</strong> <span id="projAvgDisp" style="color:var(--green);font-weight:700">0%</span>
              </div>
            </div>

            <!-- QUIZZES — 10 clickable cards -->
            <div class="card">
              <div class="card-hd">
                <div>
                  <div class="card-title"><i class="fas fa-question-circle" style="color:var(--accent);margin-right:8px"></i>Quiz Scores</div>
                  <div class="card-sub">10 quizzes, each out of 10 marks. Click any card to enter or update a score.</div>
                </div>
              </div>
              <div class="quiz-grid" id="quizGrid"><!-- rendered by JS --></div>
              <div class="totals-bar">
                <strong>Total:</strong> <span id="quizTotalDisp" style="font-family:'Syne',sans-serif;font-weight:800;color:var(--primary)">0 / 100</span>
                &nbsp;|&nbsp; <strong>Avg:</strong> <span id="quizAvgDisp" style="color:var(--green);font-weight:700">0%</span>
              </div>
            </div>

            <!-- BONUS -->
            <div class="card">
              <div class="card-hd">
                <div>
                  <div class="card-title"><i class="fas fa-star" style="color:var(--gold);margin-right:8px"></i>Bonus Marks</div>
                  <div class="card-sub">Reward participation, consistency, and extra contributions. Max 10 bonus points.</div>
                </div>
              </div>
              <div class="form-row">
                <div class="fg"><label class="lbl">Bonus (0–10)</label><input class="inp" type="number" id="bonusScore" placeholder="0" min="0" max="10" step="0.5"></div>
                <div class="fg"><label class="lbl">Reason / Notes</label><input class="inp" id="bonusReason" placeholder="e.g. Active in all group sessions"></div>
              </div>
              <div style="display:flex;gap:10px;flex-wrap:wrap">
                <button class="btn btn-green" onclick="saveBonus()"><i class="fas fa-save"></i> Save Bonus</button>
                <button class="btn btn-danger" onclick="deleteBonus()"><i class="fas fa-trash"></i> Reset Bonus</button>
              </div>
            </div>

            <!-- ACTIVITY -->
            <div class="card">
              <div class="card-hd">
                <div>
                  <div class="card-title"><i class="fas fa-users" style="color:var(--gold);margin-right:8px"></i>Activity & Q&A</div>
                  <div class="card-sub">Track participation, questions answered, and engagement</div>
                </div>
              </div>
              <div class="form-row three">
                <div class="fg"><label class="lbl">Group Activity (0–100)</label><input class="inp" type="number" id="activityScore" placeholder="70" min="0" max="100"></div>
                <div class="fg"><label class="lbl">Questions Answered</label><input class="inp" type="number" id="questionsAnswered" placeholder="12" min="0"></div>
                <div class="fg"><label class="lbl">Total Sessions Held</label><input class="inp" type="number" id="totalSessions" placeholder="10" min="1"></div>
              </div>
              <button class="btn btn-ghost" onclick="saveActivityScore()"><i class="fas fa-save"></i> Save Activity</button>
            </div>
          </div>
        </div>

        <!-- ═══════════════════ STUDENTS ═══════════════════════ -->
        <div class="section-panel" id="sec-scholars">
          <div class="card">
            <div class="card-hd">
              <div>
                <div class="card-title">My Assigned Students</div>
                <div class="card-sub" id="scholarsSubtitle">Your student group</div>
              </div>
              <button class="btn btn-blue" style="font-size:12px;padding:8px 14px" onclick="loadAllScholars()"><i class="fas fa-sync"></i> Refresh</button>
            </div>
            <div class="tbl-wrap">
              <table>
                <thead><tr><th>Name</th><th>Scholar ID</th><th>Course</th><th>Attendance</th><th>Projects</th><th>Quizzes</th><th>Bonus</th><th>Actions</th></tr></thead>
                <tbody id="scholarsBody"><tr><td class="empty-row" colspan="8">Click Refresh to load your students.</td></tr></tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- ═══════════════════ ACTIVITY ═══════════════════════ -->
        <div class="section-panel" id="sec-activity">
          <div class="card">
            <div class="card-hd">
              <div><div class="card-title">Log Session Activity</div><div class="card-sub">Record Q&A and participation for your students</div></div>
            </div>
            <div style="display:flex;gap:10px;margin-bottom:16px">
              <input class="inp" type="text" id="actSearchId" placeholder="Scholar ID" style="flex:1" oninput="this.value=this.value.toLowerCase().replace(/[^a-z0-9_]/g,'')">
              <button class="btn btn-blue" onclick="findScholarActivity()"><i class="fas fa-search"></i> Find</button>
            </div>
            <div class="access-denied" id="actAccessDenied"><i class="fas fa-lock"></i><div>This scholar is not in your assigned group.</div></div>
            <div id="activityEditor" style="display:none">
              <div style="margin-bottom:14px;font-size:14px;color:var(--muted)">Logging for: <strong id="actScholarName" style="color:var(--ink)">—</strong></div>
              <div class="form-row three">
                <div class="fg"><label class="lbl">Session / Date</label><input class="inp" id="actSession" placeholder="Week 3 — 12 Jul 2025"></div>
                <div class="fg"><label class="lbl">Questions Answered</label><input class="inp" type="number" id="actQCount" placeholder="3" min="0"></div>
                <div class="fg">
                  <label class="lbl">Group Participation</label>
                  <select class="inp" id="actParticipation">
                    <option value="active">Active (+5 pts)</option>
                    <option value="moderate">Moderate (+2 pts)</option>
                    <option value="low">Low (+1 pt)</option>
                    <option value="absent">Absent (+0 pts)</option>
                  </select>
                </div>
              </div>
              <button class="btn btn-green" onclick="saveActivityLog()"><i class="fas fa-plus"></i> Log Activity</button>
            </div>
          </div>
          <div class="card">
            <div class="card-hd">
              <div><div class="card-title">Top Active Students</div><div class="card-sub">From your group, ranked by questions answered</div></div>
              <button class="btn btn-blue" style="font-size:12px;padding:8px 14px" onclick="loadTopActive()"><i class="fas fa-sync"></i> Load</button>
            </div>
            <div class="tbl-wrap">
              <table>
                <thead><tr><th>#</th><th>Scholar</th><th>Questions</th><th>Activity Score</th><th>Sessions Attended</th></tr></thead>
                <tbody id="topActiveBody"><tr><td class="empty-row" colspan="5">Click Load to view rankings.</td></tr></tbody>
              </table>
            </div>
          </div>
        </div>

      </div><!-- /dash-body -->
    </div><!-- /main -->

    <!-- PROJECT MODAL -->
    <div class="modal-overlay" id="projModal">
      <div class="modal">
        <div class="modal-hd">
          <div><h3 id="projModalTitle">Project Score</h3><p id="projModalSub">—</p></div>
          <button class="modal-close" onclick="closeModal('projModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-bd">
          <div class="fg"><label class="lbl">Score (out of 10)</label><input class="inp" type="number" id="projModalScore" min="0" max="10" step="0.5" placeholder="0"></div>
          <div class="fg"><label class="lbl">Date Submitted</label><input class="inp" type="date" id="projModalDate"></div>
          <div class="fg"><label class="lbl">Instructor Feedback (optional)</label><input class="inp" id="projModalFeedback" placeholder="e.g. Great structure, improve comments…"></div>
        </div>
        <div class="modal-ft">
          <button class="btn btn-danger" onclick="confirmDeleteProject()"><i class="fas fa-trash"></i> Reset to 0</button>
          <button class="btn btn-ghost" onclick="closeModal('projModal')">Cancel</button>
          <button class="btn btn-green" onclick="saveProjectScore()"><i class="fas fa-save"></i> Save Score</button>
        </div>
      </div>
    </div>

    <!-- QUIZ MODAL -->
    <div class="modal-overlay" id="quizModal">
      <div class="modal">
        <div class="modal-hd">
          <div><h3 id="quizModalTitle">Quiz Score</h3><p id="quizModalSub">—</p></div>
          <button class="modal-close" onclick="closeModal('quizModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-bd">
          <div class="fg"><label class="lbl">Score (out of 10)</label><input class="inp" type="number" id="quizModalScore" min="0" max="10" step="0.5" placeholder="0"></div>
          <div class="fg"><label class="lbl">Date Taken</label><input class="inp" type="date" id="quizModalDate"></div>
        </div>
        <div class="modal-ft">
          <button class="btn btn-danger" onclick="confirmDeleteQuiz()"><i class="fas fa-trash"></i> Reset to 0</button>
          <button class="btn btn-ghost" onclick="closeModal('quizModal')">Cancel</button>
          <button class="btn btn-green" onclick="saveQuizScore()"><i class="fas fa-save"></i> Save Score</button>
        </div>
      </div>
    </div>

    <div class="toast-wrap" id="toastWrap"></div>

    <!-- FIREBASE -->
    <script type="module">
    import { initializeApp } from 'https://www.gstatic.com/firebasejs/10.12.0/firebase-app.js';
    import { getFirestore,collection,query,where,getDocs,getDoc,doc,updateDoc,addDoc,arrayUnion } from 'https://www.gstatic.com/firebasejs/10.12.0/firebase-firestore.js';
    import { getAuth,signInWithEmailAndPassword,signOut,onAuthStateChanged } from 'https://www.gstatic.com/firebasejs/10.12.0/firebase-auth.js';
    const cfg={apiKey:"AIzaSyCZdRCLRLMeiV3mtyRB0ACJ5ihXMrR_yV0",authDomain:"easyprimetech-scholarship.firebaseapp.com",projectId:"easyprimetech-scholarship",storageBucket:"easyprimetech-scholarship.firebasestorage.app",messagingSenderId:"7870182921",appId:"1:7870182921:web:d5478fe6188db8ae8eaad8"};
    const app=initializeApp(cfg),db=getFirestore(app),auth=getAuth(app);
    window.db=db;window.auth=auth;
    window.FS={collection,query,where,getDocs,getDoc,doc,updateDoc,addDoc,arrayUnion};
    window.signInFn=(e,p)=>signInWithEmailAndPassword(auth,e,p);
    window.signOutFn=()=>signOut(auth);
    onAuthStateChanged(auth,u=>{if(window.onAuthChanged)window.onAuthChanged(u);});
    </script>

    <script>
      // ══════════════════════════════════════════════════════
      // CONSTANTS
      // ══════════════════════════════════════════════════════
      const SCORE_PER_SESSION = 2.5; // 40% ÷ 16 sessions
      const PROJ_MAX = 10, QUIZ_MAX = 10, PROJ_COUNT = 4, QUIZ_COUNT = 10;

      // ══════════════════════════════════════════════════════
      // STATE
      // ══════════════════════════════════════════════════════
      let currentUser=null,instrData=null,curSection='sessions';
      let curScholarDocId=null,curScholarData=null;
      let activeSessionId=null,pollInterval=null;
      let allMyScholars=[];
      let activeProjId=null,activeQuizId=null;

      // ══════════════════════════════════════════════════════
      // UTILITIES
      // ══════════════════════════════════════════════════════
      function esc(s){return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;')}
      function toast(msg,type='success'){
        const w=document.getElementById('toastWrap');
        const el=document.createElement('div');el.className='toast-item '+type;
        const ic={success:'fa-check-circle',error:'fa-times-circle',info:'fa-info-circle',warn:'fa-exclamation-triangle'};
        el.innerHTML=`<i class="fas ${ic[type]||ic.info}"></i> ${msg}`;
        w.appendChild(el);
        setTimeout(()=>{el.style.transition='opacity .3s';el.style.opacity='0';setTimeout(()=>el.remove(),300);},3500);
      }
      function openModal(id){document.getElementById(id).classList.add('show')}
      function closeModal(id){document.getElementById(id).classList.remove('show')}
      function getIds(){return instrData?.studentIds||null}
      function isMyStudent(id){const ids=getIds();if(!ids)return true;return ids.includes(id)}
      function genCode(){const c='ABCDEFGHJKLMNPQRSTUVWXYZ23456789';return Array.from({length:6},()=>c[Math.floor(Math.random()*c.length)]).join('')}

      // ── SCORE CALCULATION ──────────────────────────────────
      function calcAtt(data){
        const raw = data.attendanceSessions || data.attendance || [];
        // Deduplicate by week+classNum — prefer present/marked over absent
        const map = new Map();
        raw.forEach(s => {
          const key = (s.week || '?') + '_' + (s.classNum || '?');
          const existing = map.get(key);
          if (!existing || s.status === 'present' || s.status === 'marked') {
            map.set(key, s);
          }
        });
        let earned = 0;
        map.forEach(s => { if(s.status==='present'||s.status==='marked') earned += SCORE_PER_SESSION; });
        return { earned: Math.round(earned*10)/10, pct: Math.min(Math.round((earned/40)*100),100), deduped: Array.from(map.values()) };
      }
      function calcProjTotal(data){
        const p=data.scores?.projects||[];
        let t=0;
        for(let i=1;i<=PROJ_COUNT;i++){const x=p.find(x=>x.id==i||x.projId==i)||{};t+=Math.min(parseFloat(x.score)||0,PROJ_MAX);}
        return Math.round(t*10)/10;
      }
      function calcQuizTotal(data){
        const q=data.scores?.quizzes||[];
        let t=0;
        for(let i=1;i<=QUIZ_COUNT;i++){const x=q.find(x=>x.id==i||x.quizId==i)||{};t+=Math.min(parseFloat(x.score)||0,QUIZ_MAX);}
        return Math.round(t*10)/10;
      }

      // ── RECALCULATE + PERSIST TOTALS ──────────────────────
      async function recalcPersist(docId,data){
        const {updateDoc,doc:fDoc}=window.FS;
        const att=calcAtt(data);  // already deduplicates internally
        const proj=calcProjTotal(data);
        const quiz=calcQuizTotal(data);
        const bonus=Math.min(parseFloat(data.scores?.bonus)||0,10);
        const projPct=Math.round((proj/(PROJ_COUNT*PROJ_MAX))*100);
        const quizPct=Math.round((quiz/(QUIZ_COUNT*QUIZ_MAX))*100);
        const final=Math.min(Math.round((att.pct*40/100)+(projPct*30/100)+(quizPct*20/100)+bonus),100);
        try{
          await updateDoc(fDoc(window.db,'applications',docId),{
            'scores.attendancePct':att.pct,'scores.attendanceEarned':att.earned,
            'scores.projectTotal':proj,'scores.projectAvg':projPct,
            'scores.quizTotal':quiz,'scores.quizAvg':quizPct,
            'scores.finalScore':final,'scores.lastRecalcAt':new Date().toISOString()
          });
        }catch(e){console.warn('recalcPersist:',e)}
      }

      // ══════════════════════════════════════════════════════
      // AUTH
      // ══════════════════════════════════════════════════════
      window.onAuthChanged=async(user)=>{
        if(user){
          currentUser=user;
          await loadInstructorProfile(user.uid);
          showDashboard();
        }else{
          currentUser=null;instrData=null;
          document.getElementById('loginOverlay').style.display='flex';
          document.getElementById('mainPanel').style.display='none';
        }
      };

      async function loadInstructorProfile(uid){
        try{
          const {getDoc,doc}=window.FS;
          const snap=await getDoc(doc(window.db,'instructors',uid));
          if(snap.exists()){
            instrData=snap.data();
            if(instrData.portalAccess===false){await window.signOutFn();showLoginErr('Portal access revoked. Contact admin.');return;}
          }else{
            await window.signOutFn();
            showLoginErr('No instructor profile found. Ask admin to create your account.');
          }
        }catch(e){instrData={name:currentUser?.email?.split('@')[0]||'Instructor',studentIds:null};}
      }

      function showLoginErr(msg){
        const el=document.getElementById('loginErr');
        el.innerHTML='<i class="fas fa-exclamation-circle"></i> '+msg;
        el.style.display='block';
        document.getElementById('loginOverlay').style.display='flex';
        document.getElementById('mainPanel').style.display='none';
      }

      function showDashboard(){
        document.getElementById('loginOverlay').style.display='none';
        document.getElementById('mainPanel').style.display='flex';
        const ids=getIds();const skill=instrData?.skill||'';
        document.getElementById('sbBadge').style.display='block';
        document.getElementById('sbName').textContent=instrData?.name||currentUser?.email||'—';
        document.getElementById('sbGroup').textContent=skill?skill+' Instructor':(instrData?.assignedGroup||'');
        document.getElementById('sbCount').textContent=ids?ids.length+' student'+(ids.length===1?'':'s')+' assigned':'All students';
        document.getElementById('scholarsSubtitle').textContent=instrData?.assignedGroup?'Group: '+instrData.assignedGroup:'All students';
        if(skill){document.getElementById('skillBannerName').textContent=skill;document.getElementById('skillBanner').classList.add('show');}
        loadSessionStats();loadPastSessions();
      }

      async function doLogin(){
        const email=document.getElementById('loginEmail').value.trim();
        const pass=document.getElementById('loginPass').value;
        if(!email||!pass){alert('Please enter email and password.');return;}
        const btn=document.getElementById('loginBtn');const err=document.getElementById('loginErr');
        btn.disabled=true;btn.innerHTML='<span class="spinner"></span> Signing in...';err.style.display='none';
        try{await window.signInFn(email,pass);}
        catch(e){
          let msg='Incorrect email or password.';
          if(e.code==='auth/too-many-requests')msg='Too many attempts. Please wait.';
          else if(e.code==='auth/network-request-failed')msg='Network error. Check your connection.';
          err.innerHTML='<i class="fas fa-exclamation-circle"></i> '+msg;err.style.display='block';
          btn.disabled=false;btn.innerHTML='<i class="fas fa-lock"></i> Sign In';
        }
      }
      async function doLogout(){await window.signOutFn();}

      // ══════════════════════════════════════════════════════
      // NAVIGATION
      // ══════════════════════════════════════════════════════
      function showSection(name,el){
        document.querySelectorAll('.section-panel').forEach(p=>p.classList.remove('active'));
        document.querySelectorAll('.sb-item').forEach(i=>i.classList.remove('active'));
        document.getElementById('sec-'+name).classList.add('active');
        if(el)el.classList.add('active');
        curSection=name;
        const titles={sessions:'Class Sessions',scores:'Update Scores',scholars:'My Students',activity:'Activity & Q&A',attendance:'Attendance Logs'};
        document.getElementById('topbarTitle').textContent=titles[name]||name;
        if(name==='attendance')loadAttendanceLogs();
        if(window.innerWidth<=768)closeSidebar();
      }
      function refreshCurrent(){
        if(curSection==='sessions'){loadSessionStats();loadPastSessions();}
        else if(curSection==='scholars')loadAllScholars();
        else if(curSection==='activity')loadTopActive();
        else if(curSection==='attendance')loadAttendanceLogs();
        else if(curSection==='scores'&&curScholarDocId)reloadCurrentScholar();
      }
      function toggleSidebar(){document.getElementById('sidebar').classList.toggle('open');document.getElementById('sbOverlay').classList.toggle('show');}
      function closeSidebar(){document.getElementById('sidebar').classList.remove('open');document.getElementById('sbOverlay').classList.remove('show');}

      // ══════════════════════════════════════════════════════
      // SESSIONS
      // ══════════════════════════════════════════════════════
      async function loadSessionStats(){
        try{
          const {getDocs,collection,query,where}=window.FS;
          const snap=await getDocs(collection(window.db,'attendance_sessions'));
          const mine=snap.docs.map(d=>({id:d.id,...d.data()})).filter(s=>s.createdBy===currentUser?.uid);
          const ids=getIds();
          let studentCount;
          if(ids){studentCount=ids.length;}
          else{const ap=await getDocs(query(collection(window.db,'applications'),where('status','==','accepted')));studentCount=ap.size;}
          document.getElementById('sSessions').textContent=studentCount;
          document.getElementById('sAttendances').textContent=mine.length;
          document.getElementById('sActive').textContent=mine.reduce((s,x)=>s+(x.attendees||[]).length,0);
          document.getElementById('sScholars').textContent=mine.filter(s=>s.active).length;
        }catch(e){console.error(e);}
      }

      async function createSession(){
        const name=document.getElementById('sessionName').value.trim();
        const topic=document.getElementById('sessionTopic').value.trim();
        const week=parseInt(document.getElementById('sessionWeek').value);
        const classN=parseInt(document.getElementById('sessionClass').value);
        if(!name){alert('Please enter a session name.');return;}
        if(!topic){alert('Please enter today\'s topic.');return;}
        const btn=document.getElementById('createSessionBtn');
        btn.disabled=true;btn.innerHTML='<span class="spinner-dark"></span> Creating...';
        const code=genCode();
        // Fetch server time so expiresAt is based on true UTC, not instructor's device clock
        let now=new Date();
        try{
          const tr=await fetch('https://worldtimeapi.org/api/timezone/Etc/UTC',{cache:'no-store'});
          if(tr.ok){const td=await tr.json();now=new Date(td.utc_datetime);}
        }catch(e){/* use device time if API unreachable */}
        const expiresAt=new Date(now.getTime()+30*60*1000);
        try{
          const {addDoc,collection}=window.FS;
          const ref=await addDoc(collection(window.db,'attendance_sessions'),{
            code,sessionName:name,name,topic,week,classNum:classN,active:true,scope:'instructor',
            instructorId:currentUser?.uid,instructorName:instrData?.name||'',
            instructorSkill:instrData?.skill||'',instructorStudentIds:getIds(),
            createdAt:now.toISOString(),expiresAt:expiresAt.toISOString(),createdBy:currentUser?.uid,
            attendees:[],date:now.toLocaleDateString('en-GB'),dateISO:now.toISOString().split('T')[0]
          });
          activeSessionId=ref.id;
          document.getElementById('activeCodeDisplay').textContent=code;
          document.getElementById('activeSessionMeta').textContent=`${name} · Week ${week} Class ${classN} · ${topic}`;
          document.getElementById('activeSessionCard').style.display='block';
          document.getElementById('attendeeCount').textContent='0 students marked attendance';
          startPolling(ref.id);startCountdown(expiresAt,ref.id);
          loadPastSessions();loadSessionStats();
          toast('Session created! Code: '+code);
          document.getElementById('sessionName').value='';document.getElementById('sessionTopic').value='';
        }catch(e){alert('Error creating session: '+e.message);}
        finally{btn.disabled=false;btn.innerHTML='<i class="fas fa-plus"></i> Create Session & Generate Code';}
      }

      function startCountdown(expiresAt,sessionId){
        if(window._cd)clearInterval(window._cd);
        const el=document.getElementById('sessionCountdown');el.style.display='inline-block';
        window._cd=setInterval(async()=>{
          const rem=new Date(expiresAt)-new Date();
          if(rem<=0){
            clearInterval(window._cd);
            el.textContent='Expired — marking absent students…';el.style.color='var(--red)';
            await autoMarkAbsent(sessionId);
            const{updateDoc,doc}=window.FS;
            updateDoc(doc(window.db,'attendance_sessions',sessionId),{active:false}).catch(()=>{});
            document.getElementById('activeSessionCard').style.display='none';
            activeSessionId=null;if(pollInterval)clearInterval(pollInterval);
            toast('Session expired. Absent students recorded.','warn');
            loadPastSessions();loadSessionStats();return;
          }
          const m=Math.floor(rem/60000);const s=Math.floor((rem%60000)/1000);
          el.textContent=`Expires in: ${m}m ${s}s`;
          el.style.color=rem<300000?'var(--accent)':'var(--green)';
        },1000);
      }

      // ── AUTO MARK ABSENT ──────────────────────────────────
      async function autoMarkAbsent(sessionId){
        try{
          const{getDoc,getDocs,updateDoc,collection,query,where,doc}=window.FS;
          const sessSnap=await getDoc(doc(window.db,'attendance_sessions',sessionId));
          if(!sessSnap.exists())return;
          const sData=sessSnap.data();
          const attendees=new Set(sData.attendees||[]);
          const week=sData.week||null;const classN=sData.classNum||null;
          const myIds=getIds();
          const appQ=query(collection(window.db,'applications'),where('status','==','accepted'));
          const appSnap=await getDocs(appQ);
          let scholars=appSnap.docs.map(d=>({id:d.id,...d.data()}));
          if(myIds)scholars=scholars.filter(s=>myIds.includes(s.username));
          const now=new Date();
          const promises=[];
          for(const s of scholars){
            if(attendees.has(s.username))continue;
            const existing=(s.attendanceSessions||[]).find(r=>r.week==week&&r.classNum==classN);
            if(existing)continue;
            const absentRec={
              sessionId,sessionName:sData.name||sData.sessionName||'Session',
              week,classNum:classN,status:'absent',score:0,
              date:now.toISOString().split('T')[0],
              time:now.toLocaleTimeString('en-GB',{hour:'2-digit',minute:'2-digit'}),
              markedAt:now.toLocaleString('en-GB'),autoMarked:true,timestamp:now.toISOString()
            };
            const newSessions=[...(s.attendanceSessions||[]),absentRec];
            const newAtt=[...(s.attendance||[]),{...absentRec}];
            const ref=doc(window.db,'applications',s.id);
            promises.push(
              updateDoc(ref,{attendanceSessions:newSessions,attendance:newAtt}).then(async()=>{
                const fresh=await getDoc(ref);
                if(fresh.exists())await recalcPersist(s.id,fresh.data());
              }).catch(e=>console.warn('auto-absent:',s.username,e))
            );
          }
          await Promise.all(promises);
        }catch(e){console.error('autoMarkAbsent:',e);}
      }

      function startPolling(sessionId){
        if(pollInterval)clearInterval(pollInterval);
        pollInterval=setInterval(async()=>{
          try{const{getDoc,doc}=window.FS;const snap=await getDoc(doc(window.db,'attendance_sessions',sessionId));
          if(snap.exists()){const n=(snap.data().attendees||[]).length;document.getElementById('attendeeCount').textContent=`${n} student${n===1?'':'s'} marked attendance`;}}catch(e){}
        },6000);
      }

      async function closeSession(){
        if(!activeSessionId)return;
        if(!confirm('Close this session? Absent students will be recorded automatically.'))return;
        try{
          const{updateDoc,doc}=window.FS;
          await updateDoc(doc(window.db,'attendance_sessions',activeSessionId),{active:false});
          await autoMarkAbsent(activeSessionId);
          document.getElementById('activeSessionCard').style.display='none';
          if(pollInterval)clearInterval(pollInterval);if(window._cd)clearInterval(window._cd);
          activeSessionId=null;
          toast('Session closed. Absent students recorded.','info');
          loadPastSessions();loadSessionStats();
        }catch(e){alert('Error: '+e.message);}
      }
      function copyCode(){const c=document.getElementById('activeCodeDisplay').textContent;navigator.clipboard.writeText(c).then(()=>toast('Code copied: '+c));}

      async function loadPastSessions(){
        const body=document.getElementById('pastSessionsBody');
        try{
          const{getDocs,collection}=window.FS;
          const snap=await getDocs(collection(window.db,'attendance_sessions'));
          const sessions=snap.docs.map(d=>({id:d.id,...d.data()}))
            .filter(s=>s.createdBy===currentUser?.uid)
            .sort((a,b)=>(b.createdAt||'').localeCompare(a.createdAt||''));
          if(!sessions.length){body.innerHTML='<tr><td class="empty-row" colspan="7">No sessions yet.</td></tr>';return;}
          body.innerHTML=sessions.map(s=>{
            const isLive=s.active&&(s.expiresAt?new Date(s.expiresAt)>new Date():true);
            const status=isLive?'<span class="live-badge">Live</span>':'<span class="closed-badge">Closed</span>';
            const week=s.week?`W${s.week} C${s.classNum||'?'}`:'—';
            return `<tr>
              <td><strong>${esc(s.name||'Session')}</strong>${s.topic?`<br><small style="color:var(--muted)">${esc(s.topic)}</small>`:''}
              </td>
              <td style="font-family:'Syne',sans-serif;font-size:12px;font-weight:700;color:var(--primary)">${week}</td>
              <td style="font-size:12px">${esc(s.date||'—')}</td>
              <td style="font-family:'Syne',sans-serif;font-size:16px;font-weight:800;letter-spacing:3px;color:var(--primary)">${esc(s.code||'')}</td>
              <td><strong>${(s.attendees||[]).length}</strong></td>
              <td>${status}</td>
              <td>${s.active?`<button class="btn btn-danger" style="padding:5px 10px;font-size:11px" onclick="closeSessionById('${s.id}')"><i class="fas fa-stop"></i> Close</button>`:''}</td>
            </tr>`;
          }).join('');
        }catch(e){body.innerHTML=`<tr><td colspan="7" style="color:var(--red);padding:16px">Error: ${esc(e.message)}</td></tr>`;}
      }

      async function closeSessionById(id){
        if(!confirm('Close this session and record absent students?'))return;
        try{
          const{updateDoc,doc}=window.FS;
          await updateDoc(doc(window.db,'attendance_sessions',id),{active:false});
          await autoMarkAbsent(id);
          toast('Session closed.','info');loadPastSessions();loadSessionStats();
        }catch(e){alert('Error: '+e.message);}
      }

      // ══════════════════════════════════════════════════════
      // ATTENDANCE LOGS
      // ══════════════════════════════════════════════════════
      async function loadAttendanceLogs(){
        const tbody=document.getElementById('attLogsBody');
        tbody.innerHTML='<tr><td class="empty-row" colspan="7">Loading...</td></tr>';
        const myIds=getIds();
        try{
          const{getDocs,collection,query,where}=window.FS;
          const appQ=query(collection(window.db,'applications'),where('status','==','accepted'));
          const appSnap=await getDocs(appQ);
          let scholars=appSnap.docs.map(d=>({id:d.id,...d.data()}));
          if(myIds)scholars=scholars.filter(s=>myIds.includes(s.username));
          allMyScholars=scholars;

          // Sessions for dropdown
          const sessSnap=await getDocs(collection(window.db,'attendance_sessions'));
          const sessions=sessSnap.docs.map(d=>({id:d.id,...d.data()}))
            .filter(s=>s.createdBy===currentUser?.uid)
            .sort((a,b)=>(b.createdAt||'').localeCompare(a.createdAt||''));
          const filter=document.getElementById('sessionFilter');
          const cur=filter.value;
          filter.innerHTML='<option value="">All sessions</option>'+
            sessions.map(s=>`<option value="${s.id}">${esc(s.name||'Session')} — ${s.date||''}${s.active?' (Active)':''}</option>`).join('');
          if(cur)filter.value=cur;

          renderAttendanceView();

          // Flat log
          const filterSessId=filter.value;
          const rows=[];
          for(const s of scholars){
            const recs=s.attendanceSessions||s.attendance||[];
            for(const r of recs){
              if(filterSessId&&r.sessionId!==filterSessId)continue;
              const st=r.status||'present';
              const score=(st==='present'||st==='marked')?'+'+SCORE_PER_SESSION+'%':'0%';
              rows.push({name:s.fullName||s.username,scholarId:s.username,docId:s.id,
                session:r.sessionName||'Session',sessionId:r.sessionId||'',status:st,score,date:r.markedAt||r.date||'—',timestamp:r.timestamp||''});
            }
          }
          rows.sort((a,b)=>(b.timestamp||'').localeCompare(a.timestamp||''));
          if(!rows.length){tbody.innerHTML='<tr><td class="empty-row" colspan="7">No records found.</td></tr>';document.getElementById('attSummary').style.display='none';return;}

          const sb=s=>{
            if(s==='present'||s==='marked')return'<span class="badge badge-present">Present</span>';
            if(s==='absent')return'<span class="badge badge-absent">Absent</span>';
            if(s==='missed')return'<span class="badge badge-missed">Missed</span>';
            return`<span class="badge" style="background:#f3f4f6;color:var(--muted)">${s}</span>`;
          };
          tbody.innerHTML=rows.map(r=>`<tr>
            <td><strong>${esc(r.name)}</strong></td>
            <td style="font-family:'Syne',sans-serif;font-size:12px;font-weight:700;color:var(--primary)">${esc(r.scholarId)}</td>
            <td style="font-size:12px">${esc(r.session)}</td>
            <td>${sb(r.status)}</td>
            <td style="font-family:'Syne',sans-serif;font-weight:700;color:${r.score.startsWith('+')?'var(--green)':'var(--red)'}">${r.score}</td>
            <td style="font-size:12px;white-space:nowrap">${esc(r.date)}</td>
            <td><button class="btn btn-danger" style="padding:4px 9px;font-size:11px" onclick="deleteAttBySession('${r.docId}','${esc(r.scholarId)}','${r.sessionId||''}','${esc(r.session)}')"><i class="fas fa-trash"></i></button></td>
          </tr>`).join('');
          const u=new Set(rows.map(r=>r.scholarId)).size;
          document.getElementById('attSummary').style.display='block';
          document.getElementById('attSummary').innerHTML=`<i class="fas fa-chart-bar" style="color:var(--primary);margin-right:6px"></i><strong>${rows.length}</strong> records from <strong>${u}</strong> students.`;
        }catch(e){tbody.innerHTML=`<tr><td colspan="7" style="color:var(--red);padding:16px">Error: ${esc(e.message)}</td></tr>`;}
      }

      function renderAttendanceView(){
        const wrap=document.getElementById('attendanceView');
        const nameFilter=(document.getElementById('attFilterName')?.value||'').toLowerCase();
        const weekFilter=document.getElementById('attFilterWeek')?.value||'';
        let scholars=allMyScholars;
        if(nameFilter)scholars=scholars.filter(s=>(s.fullName||s.username||'').toLowerCase().includes(nameFilter)||(s.username||'').toLowerCase().includes(nameFilter));
        if(!scholars.length){wrap.innerHTML='<div style="text-align:center;padding:32px;color:var(--muted)">No students match filter.</div>';return;}

        const statusLabel={present:'Present',marked:'Present',absent:'Absent',missed:'Missed',pending:'Pending'};
        const statusBadge=s=>{
          if(s==='present'||s==='marked')return'<span class="badge badge-present">Present</span>';
          if(s==='absent')return'<span class="badge badge-absent">Absent</span>';
          if(s==='missed')return'<span class="badge badge-missed">Missed</span>';
          return'<span class="badge" style="background:#f3f4f6;color:var(--muted)">Pending</span>';
        };
        const pillClass={present:'p-present',marked:'p-present',absent:'p-absent',missed:'p-missed',pending:'p-pending'};

        const html=scholars.map(s=>{
          // Deduplicate sessions before rendering — same logic as calcAtt
          const raw=s.attendanceSessions||[];
          const map=new Map();
          raw.forEach(r=>{
            const key=(r.week||'?')+'_'+(r.classNum||'?');
            const ex=map.get(key);
            if(!ex||r.status==='present'||r.status==='marked') map.set(key,r);
          });
          const sessions=Array.from(map.values());
          const att=calcAtt(s);
          let weeksHtml='';
          for(let w=1;w<=8;w++){
            if(weekFilter&&parseInt(weekFilter)!==w)continue;
            const c1=sessions.find(r=>r.week==w&&r.classNum==1);
            const c2=sessions.find(r=>r.week==w&&r.classNum==2);

            const pill=(cls,label,classNum)=>{
              if(!cls)return`<div class="att-pill p-pending"><span class="att-pill-lbl">${label}</span><span class="att-pill-date">Not scheduled</span><span class="att-pill-score">—</span></div>`;
              const st=cls.status||'present';
              const earned=(st==='present'||st==='marked')?'+'+SCORE_PER_SESSION+'%':'0%';
              const earnedColor=(st==='present'||st==='marked')?'var(--green)':'var(--red)';
              return`<div class="att-pill ${pillClass[st]||'p-pending'}">
                <span class="att-pill-lbl">${label}</span>
                <span class="att-pill-date">${esc(cls.date||'—')} ${esc(cls.time||'')}</span>
                ${statusBadge(st)}
                <span class="att-pill-score" style="color:${earnedColor}">${earned}</span>
                <button class="att-pill-del" onclick="deleteAttRecord('${s.id}','${esc(s.username)}',${w},${classNum})" title="Delete this attendance record"><i class="fas fa-trash"></i></button>
              </div>`;
            };

            weeksHtml+=`<div class="att-week-block">
              <div class="att-week-heading"><i class="fas fa-calendar" style="font-size:10px;opacity:.6"></i> Week ${w}</div>
              <div class="att-pills-row">
                ${pill(c1,'Class 1',1)}
                ${pill(c2,'Class 2',2)}
              </div>
            </div>`;
          }

          return`<div style="background:#fff;border:1px solid var(--border);border-radius:10px;padding:18px;margin-bottom:14px">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:8px">
              <div>
                <strong style="font-size:14px">${esc(s.fullName||s.username)}</strong>
                <span style="font-family:'Syne',sans-serif;font-size:12px;font-weight:700;color:var(--primary);margin-left:8px">${esc(s.username||'')}</span>
              </div>
              <div style="font-size:13px;color:var(--muted)">
                Attendance: <strong style="color:var(--green)">${att.earned} / 40 pts</strong>
                <span style="color:var(--muted);font-size:12px"> (${att.pct}%)</span>
              </div>
            </div>
            ${weeksHtml}
          </div>`;
        }).join('');

        wrap.innerHTML=html||'<div style="text-align:center;padding:32px;color:var(--muted)">No data.</div>';
      }

      // ── DELETE ATTENDANCE RECORD ────────────────────────────
      async function deleteAttRecord(docId,scholarId,week,classNum){
        if(!confirm(`Remove attendance for ${scholarId} — Week ${week} Class ${classNum}?\nThis will fully delete this record and recalculate their score.`))return;
        try{
          const{getDoc,updateDoc,doc}=window.FS;
          const ref=doc(window.db,'applications',docId);
          const snap=await getDoc(ref);if(!snap.exists()){toast('Student not found.','error');return;}
          const data=snap.data();
          const attSessions=(data.attendanceSessions||[]).filter(r=>!(r.week==week&&r.classNum==classNum));
          const legAtt=(data.attendance||[]).filter(r=>!(r.week==week&&r.classNum==classNum));
          await updateDoc(ref,{attendanceSessions:attSessions,attendance:legAtt});
          const fresh=await getDoc(ref);if(fresh.exists())await recalcPersist(docId,fresh.data());
          const si=allMyScholars.findIndex(s=>s.id===docId);
          if(si>=0){allMyScholars[si].attendanceSessions=attSessions;allMyScholars[si].attendance=legAtt;}
          toast('Attendance record removed.','info');
          renderAttendanceView();
          loadAttendanceLogs();
        }catch(e){toast('Error: '+e.message,'error');}
      }

      // ── DELETE ATTENDANCE BY SESSION ID (flat log) ────────────────────
      async function deleteAttBySession(docId,scholarId,sessionId,sessionName){
        if(!confirm(`Remove attendance for ${scholarId} — "${sessionName}"?\nThis will fully delete this record and recalculate their score.`))return;
        try{
          const{getDoc,updateDoc,doc}=window.FS;
          const ref=doc(window.db,'applications',docId);
          const snap=await getDoc(ref);if(!snap.exists()){toast('Student not found.','error');return;}
          const data=snap.data();
          const attSessions=(data.attendanceSessions||[]).filter(r=>r.sessionId!==sessionId);
          const legAtt=(data.attendance||[]).filter(r=>r.sessionId!==sessionId);
          await updateDoc(ref,{attendanceSessions:attSessions,attendance:legAtt});
          const fresh=await getDoc(ref);if(fresh.exists())await recalcPersist(docId,fresh.data());
          const si=allMyScholars.findIndex(s=>s.id===docId);
          if(si>=0){allMyScholars[si].attendanceSessions=attSessions;allMyScholars[si].attendance=legAtt;}
          toast('Attendance record removed.','info');
          loadAttendanceLogs();
        }catch(e){toast('Error: '+e.message,'error');}
      }

      // ══════════════════════════════════════════════════════
      // SCORES TAB — FIND SCHOLAR
      // ══════════════════════════════════════════════════════
      async function findScholar(){
        const scholarId=document.getElementById('scoreSearchId').value.trim();if(!scholarId)return;
        document.getElementById('scholarNotFound').classList.remove('show');
        document.getElementById('accessDenied').classList.remove('show');
        document.getElementById('scoreEditor').style.display='none';
        if(!isMyStudent(scholarId)){document.getElementById('accessDenied').classList.add('show');return;}
        try{
          const{getDocs,collection,query,where}=window.FS;
          const snap=await getDocs(query(collection(window.db,'applications'),where('username','==',scholarId)));
          if(snap.empty||snap.docs[0].data().status!=='accepted'){document.getElementById('scholarNotFound').classList.add('show');return;}
          curScholarDocId=snap.docs[0].id;curScholarData=snap.docs[0].data();
          renderScoreEditor();
        }catch(e){alert('Error: '+e.message);}
      }

      async function reloadCurrentScholar(){
        if(!curScholarDocId)return;
        const{getDoc,doc}=window.FS;
        const snap=await getDoc(doc(window.db,'applications',curScholarDocId));
        if(snap.exists()){curScholarData=snap.data();renderScoreEditor();}
      }

      function renderScoreEditor(){
        const data=curScholarData;if(!data)return;
        document.getElementById('seScholarName').textContent=data.fullName||data.username||'—';
        document.getElementById('seScholarId').textContent='Scholar ID: '+(data.username||'')+(data.assignedSkill?' · '+data.assignedSkill:data.firstChoice?' · '+data.firstChoice:'');
        document.getElementById('scoreEditor').style.display='block';
        // Summary
        const att=calcAtt(data);const proj=calcProjTotal(data);const quiz=calcQuizTotal(data);
        const bonus=Math.min(parseFloat(data.scores?.bonus)||0,10);
        document.getElementById('ss-att').textContent=att.earned+'/40';
        document.getElementById('ss-proj').textContent=proj+'/'+(PROJ_COUNT*PROJ_MAX);
        document.getElementById('ss-quiz').textContent=quiz+'/'+(QUIZ_COUNT*QUIZ_MAX);
        document.getElementById('ss-bonus').textContent=bonus+'/10';
        // Bonus & activity fields
        document.getElementById('bonusScore').value=bonus;
        document.getElementById('bonusReason').value=data.scores?.bonusReason||'';
        document.getElementById('activityScore').value=data.scores?.activityScore||'';
        document.getElementById('questionsAnswered').value=data.scores?.questionsAnswered||'';
        document.getElementById('totalSessions').value=data.scores?.totalSessions||'';
        renderProjectGrid(data);renderQuizGrid(data);
      }

      // ══════════════════════════════════════════════════════
      // PROJECT CARDS
      // ══════════════════════════════════════════════════════
      function renderProjectGrid(data){
        const projs=data.scores?.projects||[];
        let total=0;let html='';
        for(let i=1;i<=PROJ_COUNT;i++){
          const p=projs.find(x=>x.id==i||x.projId==i)||{};
          const score=Math.min(parseFloat(p.score)||0,PROJ_MAX);total+=score;
          const has=score>0;const pct=Math.round((score/PROJ_MAX)*100);
          html+=`<div class="proj-card ${has?'scored':''}">
            <div class="proj-card-title">
              <i class="fas fa-code" style="font-size:11px"></i> Project ${i}
              ${has?`<span style="font-size:10px;background:#d1fae5;color:#065f46;padding:2px 8px;border-radius:50px;margin-left:auto">${pct}%</span>`:''}
            </div>
            ${has?`
              <div class="proj-score-big">${score}<span style="font-size:14px;color:var(--muted);font-family:'DM Sans',sans-serif;font-weight:400">/${PROJ_MAX}</span></div>
              <div class="proj-score-sub">Submitted: ${esc(p.date||'—')}</div>
              ${p.feedback?`<div style="font-size:12px;color:var(--muted);font-style:italic;padding:6px 10px;background:#fff;border-radius:var(--radius);border:1px solid var(--border);margin-bottom:8px">${esc(p.feedback)}</div>`:''}
            `:`<div style="padding:16px 0;color:var(--muted);font-size:13px;text-align:center">
                <i class="fas fa-plus-circle" style="font-size:24px;opacity:.3;display:block;margin-bottom:8px"></i>No score yet
              </div>`}
            <div class="proj-actions">
              <button class="btn ${has?'btn-ghost':'btn-green'}" style="font-size:12px;padding:7px 14px" onclick="openProjModal(${i})">
                <i class="fas fa-${has?'edit':'plus'}"></i> ${has?'Edit Score':'Add Score'}
              </button>
              ${has?`<button class="btn btn-danger" style="font-size:12px;padding:7px 12px" onclick="quickDeleteProj(${i})" title="Reset to 0"><i class="fas fa-trash"></i></button>`:''}
            </div>
          </div>`;
        }
        document.getElementById('projectGrid').innerHTML=html;
        const max=PROJ_COUNT*PROJ_MAX;
        document.getElementById('projTotalDisp').textContent=Math.round(total*10)/10+' / '+max;
        document.getElementById('projAvgDisp').textContent=Math.round((total/max)*100)+'%';
      }

      function openProjModal(id){
        activeProjId=id;
        const p=(curScholarData?.scores?.projects||[]).find(x=>x.id==id||x.projId==id)||{};
        document.getElementById('projModalTitle').textContent='Project '+id;
        document.getElementById('projModalSub').textContent=(curScholarData?.fullName||'—')+' — score out of 10';
        document.getElementById('projModalScore').value=p.score||'';
        document.getElementById('projModalDate').value=p.date||new Date().toISOString().split('T')[0];
        document.getElementById('projModalFeedback').value=p.feedback||'';
        openModal('projModal');
      }

      async function saveProjectScore(){
        if(!curScholarDocId||!activeProjId)return;
        const score=parseFloat(document.getElementById('projModalScore').value);
        const date=document.getElementById('projModalDate').value||new Date().toISOString().split('T')[0];
        const feedback=document.getElementById('projModalFeedback').value.trim();
        if(isNaN(score)||score<0||score>PROJ_MAX){toast('Score must be 0–'+PROJ_MAX,'error');return;}
        try{
          const{getDoc,updateDoc,doc}=window.FS;
          const ref=doc(window.db,'applications',curScholarDocId);
          const snap=await getDoc(ref);const data=snap.data();
          let projs=[...(data.scores?.projects||[])];
          const idx=projs.findIndex(p=>p.id==activeProjId||p.projId==activeProjId);
          const entry={id:activeProjId,projId:activeProjId,score,date,feedback,savedAt:new Date().toISOString()};
          if(idx>=0)projs[idx]=entry;else projs.push(entry);
          await updateDoc(ref,{'scores.projects':projs});
          const fresh=await getDoc(ref);curScholarData=fresh.data();
          await recalcPersist(curScholarDocId,curScholarData);
          renderScoreEditor();closeModal('projModal');
          toast('Project '+activeProjId+' saved! Score: '+score+'/'+PROJ_MAX);
        }catch(e){toast('Error: '+e.message,'error');}
      }

      async function confirmDeleteProject(){if(!confirm('Reset Project '+activeProjId+' score to 0?'))return;document.getElementById('projModalScore').value=0;document.getElementById('projModalFeedback').value='';await saveProjectScore();toast('Project '+activeProjId+' reset to 0.','info');}

      async function quickDeleteProj(id){
        if(!confirm('Reset Project '+id+' score to 0?'))return;
        activeProjId=id;document.getElementById('projModalScore').value=0;
        document.getElementById('projModalDate').value=new Date().toISOString().split('T')[0];
        document.getElementById('projModalFeedback').value='';
        await saveProjectScore();
      }

      // ══════════════════════════════════════════════════════
      // QUIZ CARDS
      // ══════════════════════════════════════════════════════
      function renderQuizGrid(data){
        const quizzes=data.scores?.quizzes||[];let total=0;let html='';
        for(let i=1;i<=QUIZ_COUNT;i++){
          const q=quizzes.find(x=>x.id==i||x.quizId==i)||{};
          const score=Math.min(parseFloat(q.score)||0,QUIZ_MAX);total+=score;const has=score>0;
          html+=`<div class="quiz-card ${has?'scored':''}" onclick="openQuizModal(${i})" title="Click to edit Quiz ${i}">
            <div class="quiz-card-num">Quiz ${i}</div>
            <div class="quiz-card-score">${has?score:'—'}</div>
            <div class="quiz-card-max">${has?'/ '+QUIZ_MAX:'tap to add'}</div>
            ${has?`<div style="font-size:9px;color:var(--muted);margin-top:4px">${esc(q.date||'')}</div>`:''}
          </div>`;
        }
        document.getElementById('quizGrid').innerHTML=html;
        const max=QUIZ_COUNT*QUIZ_MAX;
        document.getElementById('quizTotalDisp').textContent=Math.round(total*10)/10+' / '+max;
        document.getElementById('quizAvgDisp').textContent=Math.round((total/max)*100)+'%';
      }

      function openQuizModal(id){
        activeQuizId=id;
        const q=(curScholarData?.scores?.quizzes||[]).find(x=>x.id==id||x.quizId==id)||{};
        document.getElementById('quizModalTitle').textContent='Quiz '+id;
        document.getElementById('quizModalSub').textContent=(curScholarData?.fullName||'—')+' — score out of 10';
        document.getElementById('quizModalScore').value=q.score||'';
        document.getElementById('quizModalDate').value=q.date||new Date().toISOString().split('T')[0];
        openModal('quizModal');
      }

      async function saveQuizScore(){
        if(!curScholarDocId||!activeQuizId)return;
        const score=parseFloat(document.getElementById('quizModalScore').value);
        const date=document.getElementById('quizModalDate').value||new Date().toISOString().split('T')[0];
        if(isNaN(score)||score<0||score>QUIZ_MAX){toast('Score must be 0–'+QUIZ_MAX,'error');return;}
        try{
          const{getDoc,updateDoc,doc}=window.FS;
          const ref=doc(window.db,'applications',curScholarDocId);
          const snap=await getDoc(ref);const data=snap.data();
          let quizzes=[...(data.scores?.quizzes||[])];
          const idx=quizzes.findIndex(q=>q.id==activeQuizId||q.quizId==activeQuizId);
          const entry={id:activeQuizId,quizId:activeQuizId,score,date,savedAt:new Date().toISOString()};
          if(idx>=0)quizzes[idx]=entry;else quizzes.push(entry);
          await updateDoc(ref,{'scores.quizzes':quizzes});
          const fresh=await getDoc(ref);curScholarData=fresh.data();
          await recalcPersist(curScholarDocId,curScholarData);
          renderScoreEditor();closeModal('quizModal');
          toast('Quiz '+activeQuizId+' saved! Score: '+score+'/'+QUIZ_MAX);
        }catch(e){toast('Error: '+e.message,'error');}
      }

      async function confirmDeleteQuiz(){if(!confirm('Reset Quiz '+activeQuizId+' to 0?'))return;document.getElementById('quizModalScore').value=0;await saveQuizScore();toast('Quiz '+activeQuizId+' reset.','info');}

      // ══════════════════════════════════════════════════════
      // BONUS
      // ══════════════════════════════════════════════════════
      async function saveBonus(){
        if(!curScholarDocId)return;
        const score=parseFloat(document.getElementById('bonusScore').value)||0;
        const reason=document.getElementById('bonusReason').value.trim();
        if(score<0||score>10){toast('Bonus must be 0–10','error');return;}
        try{
          const{getDoc,updateDoc,doc}=window.FS;const ref=doc(window.db,'applications',curScholarDocId);
          await updateDoc(ref,{'scores.bonus':score,'scores.bonusReason':reason,'scores.bonusUpdatedAt':new Date().toISOString()});
          const fresh=await getDoc(ref);curScholarData=fresh.data();
          await recalcPersist(curScholarDocId,curScholarData);renderScoreEditor();
          toast('Bonus marks saved!');
        }catch(e){toast('Error: '+e.message,'error');}
      }
      async function deleteBonus(){if(!confirm('Reset bonus marks to 0?'))return;document.getElementById('bonusScore').value=0;document.getElementById('bonusReason').value='';await saveBonus();toast('Bonus reset to 0.','info');}

      // ══════════════════════════════════════════════════════
      // ACTIVITY
      // ══════════════════════════════════════════════════════
      async function saveActivityScore(){
        if(!curScholarDocId)return;
        const act=parseInt(document.getElementById('activityScore').value)||0;
        const q=parseInt(document.getElementById('questionsAnswered').value)||0;
        const ts=parseInt(document.getElementById('totalSessions').value)||0;
        try{
          const{updateDoc,doc}=window.FS;
          await updateDoc(doc(window.db,'applications',curScholarDocId),{
            'scores.activityScore':act,'scores.questionsAnswered':q,'scores.totalSessions':ts
          });toast('Activity saved!');
        }catch(e){toast('Error: '+e.message,'error');}
      }

      // ══════════════════════════════════════════════════════
      // MY STUDENTS
      // ══════════════════════════════════════════════════════
      async function loadAllScholars(){
        const body=document.getElementById('scholarsBody');
        body.innerHTML='<tr><td class="empty-row" colspan="8">Loading...</td></tr>';
        const myIds=getIds();
        try{
          const{getDocs,collection,query,where,doc,updateDoc}=window.FS;
          const snap=await getDocs(query(collection(window.db,'applications'),where('status','==','accepted')));
          let scholars=snap.docs.map(d=>({id:d.id,...d.data()}));
          const activeSet=new Set(scholars.map(s=>s.username).filter(Boolean));
          if(myIds){
            scholars=scholars.filter(s=>myIds.includes(s.username));
            const stale=myIds.filter(id=>!activeSet.has(id));
            if(stale.length&&currentUser){
              const clean=myIds.filter(id=>activeSet.has(id));
              updateDoc(doc(window.db,'instructors',currentUser.uid),{studentIds:clean}).catch(()=>{});
              if(instrData)instrData.studentIds=clean;
              document.getElementById('sbCount').textContent=clean.length+' student'+(clean.length===1?'':'s')+' assigned';
            }
          }
          if(!scholars.length){body.innerHTML='<tr><td class="empty-row" colspan="8">No students assigned. Contact admin.</td></tr>';return;}
          body.innerHTML=scholars.map(s=>{
            const att=calcAtt(s);const proj=calcProjTotal(s);const quiz=calcQuizTotal(s);
            const bonus=Math.min(parseFloat(s.scores?.bonus)||0,10);
            const course=s.assignedSkill||s.firstChoice||(s.courses&&s.courses[0])||'—';
            return`<tr>
              <td><div style="font-weight:700;font-size:13px">${esc(s.fullName||'—')}</div><div style="font-size:11px;color:var(--muted)">${esc(s.email||'')}</div></td>
              <td style="font-family:'Syne',sans-serif;font-size:12px;font-weight:700;color:var(--primary)">${esc(s.username||'—')}</td>
              <td><span style="background:var(--light-blue);color:var(--primary);font-size:10px;padding:2px 7px;border-radius:4px;font-weight:600">${esc(course)}</span></td>
              <td>${att.earned}/40 <small style="color:var(--muted)">(${att.pct}%)</small></td>
              <td>${proj}/${PROJ_COUNT*PROJ_MAX}</td>
              <td>${quiz}/${QUIZ_COUNT*QUIZ_MAX}</td>
              <td>${bonus}</td>
              <td>
                <button class="btn btn-blue" style="padding:5px 10px;font-size:11px;margin-bottom:4px" onclick="quickEdit('${esc(s.username)}')"><i class="fas fa-pen"></i> Edit</button>
                <button class="btn btn-ghost" style="padding:5px 10px;font-size:11px" onclick="viewStudentAtt('${s.id}','${esc(s.username)}')"><i class="fas fa-calendar"></i> Attendance</button>
              </td>
            </tr>`;
          }).join('');
        }catch(e){body.innerHTML=`<tr><td class="empty-row" colspan="8" style="color:var(--red)">Error: ${esc(e.message)}</td></tr>`;}
      }

      function quickEdit(id){
        document.getElementById('scoreSearchId').value=id;
        showSection('scores',document.querySelectorAll('.sb-item')[2]);
        findScholar();
      }
      function viewStudentAtt(docId,username){
        showSection('attendance',document.querySelectorAll('.sb-item')[1]);
        document.getElementById('attFilterName').value=username;
        if(allMyScholars.length)renderAttendanceView();else loadAttendanceLogs();
      }

      // ══════════════════════════════════════════════════════
      // ACTIVITY LOG
      // ══════════════════════════════════════════════════════
      async function findScholarActivity(){
        const id=document.getElementById('actSearchId').value.trim();if(!id)return;
        document.getElementById('actAccessDenied').classList.remove('show');
        document.getElementById('activityEditor').style.display='none';
        if(!isMyStudent(id)){document.getElementById('actAccessDenied').classList.add('show');return;}
        try{
          const{getDocs,collection,query,where}=window.FS;
          const snap=await getDocs(query(collection(window.db,'applications'),where('username','==',id)));
          if(snap.empty){alert('Scholar not found.');return;}
          curScholarDocId=snap.docs[0].id;
          document.getElementById('actScholarName').textContent=snap.docs[0].data().fullName||id;
          document.getElementById('activityEditor').style.display='block';
        }catch(e){alert('Error: '+e.message);}
      }

      async function saveActivityLog(){
        if(!curScholarDocId)return;
        const session=document.getElementById('actSession').value.trim();
        const qCount=parseInt(document.getElementById('actQCount').value)||0;
        const part=document.getElementById('actParticipation').value;
        if(!session){alert('Enter session name/date.');return;}
        try{
          const{getDoc,updateDoc,doc,arrayUnion}=window.FS;
          const ref=doc(window.db,'applications',curScholarDocId);
          const snap=await getDoc(ref);const scores=snap.data().scores||{};
          const pts={active:5,moderate:2,low:1,absent:0};
          await updateDoc(ref,{
            'scores.questionsAnswered':(scores.questionsAnswered||0)+qCount,
            'scores.activityScore':Math.min(100,(scores.activityScore||0)+(pts[part]||0)),
            activityLog:arrayUnion({session,qCount,participation:part,date:new Date().toLocaleDateString('en-GB'),timestamp:new Date().toISOString()})
          });
          toast('Activity logged!');
          document.getElementById('actSession').value='';document.getElementById('actQCount').value='';
        }catch(e){alert('Error: '+e.message);}
      }

      async function loadTopActive(){
        const myIds=getIds();
        try{
          const{getDocs,collection,query,where}=window.FS;
          const snap=await getDocs(query(collection(window.db,'applications'),where('status','==','accepted')));
          let scholars=snap.docs.map(d=>d.data()).filter(s=>s.scores);
          if(myIds)scholars=scholars.filter(s=>myIds.includes(s.username));
          scholars.sort((a,b)=>(b.scores.questionsAnswered||0)-(a.scores.questionsAnswered||0));
          const body=document.getElementById('topActiveBody');
          if(!scholars.length){body.innerHTML='<tr><td class="empty-row" colspan="5">No activity data yet.</td></tr>';return;}
          const medals=['🥇','🥈','🥉'];
          body.innerHTML=scholars.slice(0,20).map((s,i)=>`<tr>
            <td style="font-family:'Syne',sans-serif;font-weight:800">${medals[i]||(i+1)+'.'}</td>
            <td>${esc(s.fullName||s.username)}</td>
            <td><strong>${s.scores.questionsAnswered||0}</strong></td>
            <td>${s.scores.activityScore||0}/100</td>
            <td>${(s.attendanceSessions||s.attendance||[]).filter(r=>r.status==='present'||r.status==='marked').length}</td>
          </tr>`).join('');
        }catch(e){alert('Error: '+e.message);}
      }

      // ══════════════════════════════════════════════════════
      // EXPOSE GLOBALS
      // ══════════════════════════════════════════════════════
      window.doLogin=doLogin;window.doLogout=doLogout;
      window.showSection=showSection;window.refreshCurrent=refreshCurrent;
      window.toggleSidebar=toggleSidebar;window.closeSidebar=closeSidebar;
      window.openModal=openModal;window.closeModal=closeModal;
      window.createSession=createSession;window.closeSession=closeSession;window.closeSessionById=closeSessionById;window.copyCode=copyCode;
      window.findScholar=findScholar;
      window.openProjModal=openProjModal;window.saveProjectScore=saveProjectScore;window.confirmDeleteProject=confirmDeleteProject;window.quickDeleteProj=quickDeleteProj;
      window.openQuizModal=openQuizModal;window.saveQuizScore=saveQuizScore;window.confirmDeleteQuiz=confirmDeleteQuiz;
      window.saveBonus=saveBonus;window.deleteBonus=deleteBonus;
      window.saveActivityScore=saveActivityScore;
      window.loadAllScholars=loadAllScholars;window.quickEdit=quickEdit;window.viewStudentAtt=viewStudentAtt;
      window.loadAttendanceLogs=loadAttendanceLogs;window.renderAttendanceView=renderAttendanceView;window.deleteAttRecord=deleteAttRecord;window.deleteAttBySession=deleteAttBySession;
      window.findScholarActivity=findScholarActivity;window.saveActivityLog=saveActivityLog;window.loadTopActive=loadTopActive;
    </script>
  </body>
</html>
