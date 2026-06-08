<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Scoreboard — EasyPrimeTech Scholarship</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles/scores.css">
  </head>
  <body>

    <nav>
      <a class="nav-logo" href="index.html">Easy<span>Prime</span>Tech</a>
      <ul class="nav-links">
        <li><a href="index.html">Home</a></li>
        <li><a href="attendance.html">Mark Attendance</a></li>
        <li><a href="status.html">Check Status</a></li>
      </ul>
      <a class="nav-cta" href="attendance.html">Mark Attendance</a>
    </nav>

    <!-- SEARCH HERO -->
    <div class="search-hero">
      <div class="eyebrow">📊 Scholar Scoreboard</div>
      <h1>View Your Scores</h1>
      <p>Enter your Scholar ID to see your full attendance breakdown, project results, quiz scores, and final grade.</p>
      <div class="search-bar">
        <input type="text" id="scholarId" placeholder="Enter your Scholar ID…" onkeydown="if(event.key==='Enter')loadScores()">
        <button id="checkBtn" onclick="loadScores()">View Scores</button>
      </div>
    </div>

    <main>
      <!-- NOT FOUND -->
      <div class="not-found" id="notFound">
        <div class="nf-icon">🔍</div>
        <h3>Scholar Not Found</h3>
        <p>We couldn't find an accepted scholar with that ID. Double-check your Scholar ID or contact your admin.</p>
      </div>

      <!-- RESULTS -->
      <div class="results" id="results">

        <!-- Profile -->
        <div class="profile-banner fade-up">
          <div class="profile-avatar" id="profileAvatar">?</div>
          <div class="profile-info">
            <div class="profile-name" id="profileName">—</div>
            <div class="profile-meta">
              <span><i class="fas fa-id-card"></i> <span id="profileId">—</span></span>
              <span><i class="fas fa-book"></i> <span id="profileCourse">—</span></span>
              <span><i class="fas fa-calendar"></i> <span id="profileDate">—</span></span>
            </div>
          </div>
          <div class="scholar-badge" id="finalBadge">—</div>
        </div>

        <!-- Stats -->
        <div class="stats-grid fade-up">
          <div class="stat-box s-accent"><div class="sval" id="statFinal">—</div><div class="slbl">Final Score</div></div>
          <div class="stat-box s-blue"><div class="sval" id="statAttendance">—</div><div class="slbl">Attendance</div></div>
          <div class="stat-box s-gold"><div class="sval" id="statProjects">—</div><div class="slbl">Projects</div></div>
          <div class="stat-box s-green"><div class="sval" id="statQuizzes">—</div><div class="slbl">Quizzes</div></div>
          <div class="stat-box s-purple"><div class="sval" id="statBonus">—</div><div class="slbl">Bonus</div></div>
        </div>

        <!-- Progress Overview -->
        <div class="sec-head fade-up"><span class="label">Overview</span><h3>Score Breakdown</h3><div class="line"></div></div>
        <div class="prog-card fade-up">
          <div class="prog-title"><i class="fas fa-chart-bar" style="color:var(--accent)"></i> Overall Performance</div>
          <div class="prog-row">
            <div class="prog-label"><i class="fas fa-calendar-check" style="color:#93b4ff;margin-right:6px"></i> Attendance (40%)</div>
            <div class="prog-track"><div class="prog-fill fill-blue" id="pb-att" style="width:0%"></div></div>
            <div class="prog-pct" id="pp-att">0%</div>
          </div>
          <div class="prog-row">
            <div class="prog-label"><i class="fas fa-project-diagram" style="color:var(--gold);margin-right:6px"></i> Projects (30%)</div>
            <div class="prog-track"><div class="prog-fill fill-gold" id="pb-proj" style="width:0%"></div></div>
            <div class="prog-pct" id="pp-proj">0%</div>
          </div>
          <div class="prog-row">
            <div class="prog-label"><i class="fas fa-question-circle" style="color:#6ee7b7;margin-right:6px"></i> Quizzes (20%)</div>
            <div class="prog-track"><div class="prog-fill fill-green" id="pb-quiz" style="width:0%"></div></div>
            <div class="prog-pct" id="pp-quiz">0%</div>
          </div>
          <div class="prog-row">
            <div class="prog-label"><i class="fas fa-star" style="color:#c4b5fd;margin-right:6px"></i> Bonus (up to 10)</div>
            <div class="prog-track"><div class="prog-fill fill-purple" id="pb-bonus" style="width:0%"></div></div>
            <div class="prog-pct" id="pp-bonus">0</div>
          </div>
          <div class="prog-row" style="margin-top:8px;padding-top:10px;border-top:1px solid rgba(255,255,255,.06)">
            <div class="prog-label" style="font-weight:800;color:#fff"><i class="fas fa-trophy" style="color:var(--accent);margin-right:6px"></i> Final Score</div>
            <div class="prog-track"><div class="prog-fill fill-accent" id="pb-final" style="width:0%"></div></div>
            <div class="prog-pct" id="pp-final" style="color:var(--accent);font-size:15px">0%</div>
          </div>
        </div>

        <!-- ATTENDANCE BREAKDOWN -->
        <div class="sec-head fade-up"><span class="label">Attendance</span><h3>Weekly Attendance Breakdown</h3><div class="line"></div></div>
        <div class="fade-up">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;flex-wrap:wrap;gap:8px">
            <div style="font-size:13px;color:rgba(255,255,255,.5)">Each class session is worth <strong style="color:var(--gold)">+2.5%</strong> attendance score (40% total ÷ 16 sessions)</div>
            <div style="font-size:13px;color:rgba(255,255,255,.5)">Total: <strong id="attTotalDisplay" style="color:#93b4ff">0 / 40</strong></div>
          </div>
          <div class="att-weeks" id="attWeeksWrap">
            <!-- Rendered by JS -->
          </div>
        </div>

        <!-- PROJECTS -->
        <div class="sec-head fade-up"><span class="label">Projects</span><h3>Project Results</h3><div class="line"></div></div>
        <div class="dt-wrap fade-up">
          <table class="dt">
            <thead><tr><th>Project</th><th>Score</th><th>Max</th><th>Grade</th><th>Date Submitted</th><th>Instructor Feedback</th></tr></thead>
            <tbody id="projectsBody"><tr><td class="empty" colspan="6">No project scores recorded yet.</td></tr></tbody>
          </table>
        </div>
        <div style="font-size:13px;color:rgba(255,255,255,.4);margin-bottom:20px;padding:0 4px" id="projSummary"></div>

        <!-- QUIZZES -->
        <div class="sec-head fade-up"><span class="label">Quizzes</span><h3>Quiz Results</h3><div class="line"></div></div>
        <div class="dt-wrap fade-up">
          <table class="dt">
            <thead><tr><th>Quiz</th><th>Score</th><th>Max</th><th>Grade</th><th>Date Taken</th></tr></thead>
            <tbody id="quizzesBody"><tr><td class="empty" colspan="5">No quiz scores recorded yet.</td></tr></tbody>
          </table>
        </div>
        <div style="font-size:13px;color:rgba(255,255,255,.4);margin-bottom:20px;padding:0 4px" id="quizSummary"></div>

        <!-- BONUS -->
        <div class="sec-head fade-up"><span class="label">Bonus</span><h3>Bonus Marks</h3><div class="line"></div></div>
        <div class="dt-wrap fade-up">
          <table class="dt">
            <thead><tr><th>Category</th><th>Points Awarded</th><th>Maximum</th><th>Notes</th></tr></thead>
            <tbody id="bonusBody"><tr><td class="empty" colspan="4">No bonus marks awarded yet.</td></tr></tbody>
          </table>
        </div>

        <!-- FINAL RESULT CARD -->
        <div class="final-card fade-up">
          <div style="font-family:'Syne',sans-serif;font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:rgba(255,255,255,.35);margin-bottom:8px">Overall Result</div>
          <div class="final-score-big" id="finalScoreBig">—</div>
          <div class="final-label" id="finalGradeLabel">—</div>
          <div class="final-breakdown">
            <div class="final-part"><div class="final-part-val" id="fbAtt">—</div><div class="final-part-lbl">Attendance</div></div>
            <div class="final-part" style="color:rgba(255,255,255,.2)">+</div>
            <div class="final-part"><div class="final-part-val" id="fbProj">—</div><div class="final-part-lbl">Projects</div></div>
            <div class="final-part" style="color:rgba(255,255,255,.2)">+</div>
            <div class="final-part"><div class="final-part-val" id="fbQuiz">—</div><div class="final-part-lbl">Quizzes</div></div>
            <div class="final-part" style="color:rgba(255,255,255,.2)">+</div>
            <div class="final-part"><div class="final-part-val" id="fbBonus">—</div><div class="final-part-lbl">Bonus</div></div>
            <div class="final-part" style="color:rgba(255,255,255,.2)">=</div>
            <div class="final-part"><div class="final-part-val" style="color:var(--accent);font-size:22px" id="fbFinal">—</div><div class="final-part-lbl">Final</div></div>
          </div>
        </div>

      </div><!-- /results -->
    </main>

    <footer>
      <div class="footer-inner">
        <div class="footer-brand">
          <h3>Easy<span>Prime</span>Tech</h3>
          <p>Making tech education accessible for everyone. Building the next generation of tech professionals.</p>
        </div>
        <div class="footer-col">
          <h4>Program</h4>
          <ul>
            <li><a href="index.html">Overview</a></li>
            <li><a href="apply.html">Apply Now</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h4>Scholar Tools</h4>
          <ul>
            <li><a href="status.html">Check Status</a></li>
            <li><a href="scores.html">My Scores</a></li>
            <li><a href="attendance.html">Mark Attendance</a></li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom">
        <span>© 2025 EasyPrimeTech Scholarship Program. All rights reserved.</span>
        <a href="status.html">Check Scholarship Status →</a>
      </div>
    </footer>

    <script type="module">
      import { initializeApp } from 'https://www.gstatic.com/firebasejs/10.12.0/firebase-app.js';
      import { getFirestore, collection, query, where, getDocs } from 'https://www.gstatic.com/firebasejs/10.12.0/firebase-firestore.js';
      import { getAuth, signInAnonymously } from 'https://www.gstatic.com/firebasejs/10.12.0/firebase-auth.js';
      const cfg={apiKey:"AIzaSyCZdRCLRLMeiV3mtyRB0ACJ5ihXMrR_yV0",authDomain:"easyprimetech-scholarship.firebaseapp.com",projectId:"easyprimetech-scholarship",storageBucket:"easyprimetech-scholarship.firebasestorage.app",messagingSenderId:"7870182921",appId:"1:7870182921:web:d5478fe6188db8ae8eaad8"};
      const app=initializeApp(cfg),db=getFirestore(app),auth=getAuth(app);
      signInAnonymously(auth).catch(e=>console.warn('Anon auth:',e.message));
      window._db=db;window._fs={collection,query,where,getDocs};window._fbReady=true;
      </script>

      <script>
      // ── CONFIG (must match instructor.html & admin.html) ─────────────────────
      const LMS = {
        WEEKS: 8,
        CLASSES_PER_WEEK: 2,
        TOTAL_SESSIONS: 16,
        ATT_WEIGHT: 40,
        PROJ_WEIGHT: 30,
        QUIZ_WEIGHT: 20,
        BONUS_MAX: 10,
        SCORE_PER_SESSION: 2.5,
        PROJECTS: 4,
        QUIZZES: 10,
        PROJECT_MAX: 10,
        QUIZ_MAX: 10,
      };

      // ── HELPERS ──────────────────────────────────────────────────────────────
      function gradeClass(pct){
        if(pct>=90) return 'g-a';
        if(pct>=75) return 'g-b';
        if(pct>=60) return 'g-c';
        if(pct>0)   return 'g-d';
        return 'g-na';
      }
      function gradeLetter(pct){
        if(pct>=90) return 'A'; if(pct>=75) return 'B'; if(pct>=60) return 'C';
        if(pct>0)  return 'D'; return 'N/A';
      }
      function finalGradeLabel(pct){
        if(pct>=90) return '🏆 Distinction';
        if(pct>=75) return '🎖 Merit';
        if(pct>=60) return '✅ Pass';
        if(pct>=40) return '⚠ Near Pass';
        return '❌ Below Pass';
      }
      function setBar(id, pctId, value, raw=false){
        setTimeout(()=>{
          const el = document.getElementById(id);
          if(el) el.style.width = Math.min(value, 100) + '%';
          const pe = document.getElementById(pctId);
          if(pe) pe.textContent = raw ? value : Math.round(value) + '%';
        }, 150);
      }
      function animateFadeUps(){
        document.querySelectorAll('.fade-up').forEach((el,i)=>{
          setTimeout(()=>el.classList.add('in'), i*50);
        });
      }
      function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

      // ── CALCULATE SCORES (mirrors instructor.html logic) ─────────────────────
      function calcScores(data){
        // ATTENDANCE — deduplicate by week+classNum, prefer 'present'/'marked' over 'absent'
        const rawSessions = data.attendanceSessions || [];
        const sessMap = new Map();
        rawSessions.forEach(s => {
          const key = (s.week || '?') + '_' + (s.classNum || '?');
          const existing = sessMap.get(key);
          // If slot already exists, only overwrite if new record is present/marked
          if (!existing || s.status === 'present' || s.status === 'marked') {
            sessMap.set(key, s);
          }
        });
        const sessions = Array.from(sessMap.values());

        let attEarned = 0;
        sessions.forEach(s=>{
          if(s.status==='present'||s.status==='marked') attEarned += LMS.SCORE_PER_SESSION;
        });
        const attPct = Math.min((attEarned / LMS.ATT_WEIGHT)*100, 100);
        const attContrib = (attPct/100) * LMS.ATT_WEIGHT;

        // PROJECTS
        const projects = data.scores?.projects || [];
        let projTotal = 0;
        for(let i=1;i<=LMS.PROJECTS;i++){
          const p = projects.find(x=>x.id==i)||{};
          projTotal += Math.min(parseFloat(p.score)||0, LMS.PROJECT_MAX);
        }
        const projMax = LMS.PROJECTS * LMS.PROJECT_MAX;
        const projPct = projMax>0?(projTotal/projMax)*100:0;
        const projContrib = (projPct/100)*LMS.PROJ_WEIGHT;

        // QUIZZES
        const quizzes = data.scores?.quizzes || [];
        let quizTotal = 0;
        for(let i=1;i<=LMS.QUIZZES;i++){
          const q = quizzes.find(x=>x.id==i)||{};
          quizTotal += Math.min(parseFloat(q.score)||0, LMS.QUIZ_MAX);
        }
        const quizMax = LMS.QUIZZES * LMS.QUIZ_MAX;
        const quizPct = quizMax>0?(quizTotal/quizMax)*100:0;
        const quizContrib = (quizPct/100)*LMS.QUIZ_WEIGHT;

        // BONUS
        const bonus = Math.min(parseFloat(data.scores?.bonus)||0, LMS.BONUS_MAX);
        const bonusReason = data.scores?.bonusReason || '';

        // FINAL
        const final = Math.min(Math.round(attContrib + projContrib + quizContrib + bonus), 100);

        return {
          attEarned: Math.round(attEarned*10)/10,
          attPct: Math.round(attPct),
          attContrib: Math.round(attContrib*10)/10,
          projTotal, projMax, projPct: Math.round(projPct),
          projContrib: Math.round(projContrib*10)/10,
          quizTotal, quizMax, quizPct: Math.round(quizPct),
          quizContrib: Math.round(quizContrib*10)/10,
          bonus, bonusReason, final,
          sessions,   // ← deduplicated array (used by renderAttendanceGrid)
          projects, quizzes
        };
      }

      // ── LOAD SCORES ──────────────────────────────────────────────────────────
      async function loadScores(){
        const scholarId = document.getElementById('scholarId').value.trim();
        if(!scholarId){ alert('Please enter your Scholar ID.'); return; }
        const btn = document.getElementById('checkBtn');
        btn.disabled = true; btn.innerHTML = '<span class="spinner"></span>';
        document.getElementById('notFound').classList.remove('show');
        document.getElementById('results').classList.remove('show');
        await new Promise(r=>setTimeout(r,500));
        try{
          const fs=window._fs, db=window._db;
          const q=fs.query(fs.collection(db,'applications'), fs.where('username','==',scholarId));
          const snap=await fs.getDocs(q);
          if(snap.empty||snap.docs[0].data().status!=='accepted'){
            document.getElementById('notFound').classList.add('show');
            return;
          }
          renderScores(snap.docs[0].data(), scholarId);
        }catch(e){
          // Fallback to localStorage
          try{
            const apps=JSON.parse(localStorage.getItem('ept_applications')||'[]');
            const found=apps.find(a=>a.username===scholarId&&a.status==='accepted');
            if(!found){ document.getElementById('notFound').classList.add('show'); return; }
            renderScores(found, scholarId);
          }catch(e2){
            document.getElementById('notFound').classList.add('show');
          }
        }finally{
          btn.disabled=false; btn.innerHTML='View Scores';
        }
      }

      // ── RENDER ───────────────────────────────────────────────────────────────
      function renderScores(data, scholarId){
        const sc = calcScores(data);

        // Profile
        const initials=(data.fullName||'?').split(' ').map(n=>n[0]).join('').toUpperCase().slice(0,2);
        document.getElementById('profileAvatar').textContent=initials;
        document.getElementById('profileName').textContent=data.fullName||scholarId;
        document.getElementById('profileId').textContent=scholarId;
        document.getElementById('profileCourse').textContent=data.assignedSkill||data.firstChoice||'—';
        document.getElementById('profileDate').textContent=data.appliedAt
          ? new Date(data.appliedAt).toLocaleDateString('en-GB',{day:'numeric',month:'short',year:'numeric'})
          : 'Scholar';

        // Badge
        const badge = finalGradeLabel(sc.final);
        document.getElementById('finalBadge').textContent = badge;
        document.getElementById('scholar-badge-color') && null;

        // Stats
        document.getElementById('statFinal').textContent=sc.final+'%';
        document.getElementById('statAttendance').textContent=sc.attEarned+'/'+LMS.ATT_WEIGHT;
        document.getElementById('statProjects').textContent=sc.projTotal+'/'+sc.projMax;
        document.getElementById('statQuizzes').textContent=sc.quizTotal+'/'+sc.quizMax;
        document.getElementById('statBonus').textContent=sc.bonus+'/'+LMS.BONUS_MAX;

        // Bars
        setBar('pb-att','pp-att',sc.attPct);
        setBar('pb-proj','pp-proj',sc.projPct);
        setBar('pb-quiz','pp-quiz',sc.quizPct);
        setBar('pb-bonus','pp-bonus',sc.bonus,true);
        setBar('pb-final','pp-final',sc.final);

        // Attendance display value
        document.getElementById('attTotalDisplay').textContent = sc.attEarned + ' / ' + LMS.ATT_WEIGHT;

        // Attendance Weekly Grid
        renderAttendanceGrid(sc.sessions);

        // Projects table
        renderProjectsTable(sc.projects, sc.projTotal, sc.projMax, sc.projPct);

        // Quizzes table
        renderQuizzesTable(sc.quizzes, sc.quizTotal, sc.quizMax, sc.quizPct);

        // Bonus
        renderBonusTable(sc.bonus, sc.bonusReason);

        // Final card
        document.getElementById('finalScoreBig').textContent = sc.final + '%';
        document.getElementById('finalGradeLabel').textContent = badge;
        document.getElementById('fbAtt').textContent = sc.attContrib + '%';
        document.getElementById('fbProj').textContent = sc.projContrib + '%';
        document.getElementById('fbQuiz').textContent = sc.quizContrib + '%';
        document.getElementById('fbBonus').textContent = '+' + sc.bonus;
        document.getElementById('fbFinal').textContent = sc.final + '%';

        document.getElementById('results').classList.add('show');
        setTimeout(animateFadeUps, 50);
      }

      // ── ATTENDANCE GRID ───────────────────────────────────────────────────────
      function renderAttendanceGrid(sessions){
        const wrap = document.getElementById('attWeeksWrap');
        const statusLabel = {present:'Attendance Marked',marked:'Attendance Marked',absent:'Absent',missed:'Attendance Missed',skipped:'Skipped',pending:'Not Scheduled'};
        const statusClass = {present:'status-marked',marked:'status-marked',absent:'status-missed',missed:'status-missed',skipped:'status-pending',pending:'status-pending'};

        let html = '';
        for(let w=1;w<=LMS.WEEKS;w++){
          const c1 = sessions.find(s=>s.week==w&&s.classNum==1);
          const c2 = sessions.find(s=>s.week==w&&s.classNum==2);

          const classHtml = (cls, label) => {
            if(!cls){
              return `<div class="att-class status-pending">
                <div class="att-class-label">${label}</div>
                <div class="att-class-status" style="color:rgba(255,255,255,.3)">Not Scheduled</div>
                <div class="att-class-date">—</div>
                <div class="att-class-score score-zero">0%</div>
              </div>`;
            }
            const status = cls.status || 'pending';
            const earned = (status==='present'||status==='marked') ? '+'+LMS.SCORE_PER_SESSION+'%' : '0%';
            const scoreClass = (status==='present'||status==='marked') ? 'score-pos' : 'score-zero';
            const pillClass = status==='present'||status==='marked' ? 's-present' : status==='missed'||status==='absent' ? 's-missed' : 's-pending';
            return `<div class="att-class ${statusClass[status]||'status-pending'}">
              <div class="att-class-label">${label}</div>
              <div class="att-class-status"><span class="s-pill ${pillClass}">${statusLabel[status]||status}</span></div>
              <div class="att-class-date">${esc(cls.date||'—')} ${esc(cls.time||'')}</div>
              <div class="att-class-score ${scoreClass}">${earned}</div>
            </div>`;
          };

          html += `<div class="att-week">
            <div class="att-week-title">Week ${w}</div>
            <div class="att-classes">
              ${classHtml(c1,'Class 1')}
              ${classHtml(c2,'Class 2')}
            </div>
          </div>`;
        }
        wrap.innerHTML = html;
      }

      // ── PROJECTS TABLE ────────────────────────────────────────────────────────
      function renderProjectsTable(projects, total, max, pct){
        const tbody = document.getElementById('projectsBody');
        let html = '';
        for(let i=1;i<=LMS.PROJECTS;i++){
          const p = projects.find(x=>x.id==i)||null;
          const score = p ? parseFloat(p.score)||0 : 0;
          const ppct = Math.round((score/LMS.PROJECT_MAX)*100);
          html += `<tr>
            <td><strong>Project ${i}</strong></td>
            <td style="font-family:'Syne',sans-serif;font-weight:800">${p?score:'—'}</td>
            <td style="color:rgba(255,255,255,.4)">${LMS.PROJECT_MAX}</td>
            <td>${p?`<span class="grade ${gradeClass(ppct)}">${gradeLetter(ppct)}</span>`:'<span class="grade g-na">N/A</span>'}</td>
            <td style="font-size:12px;color:rgba(255,255,255,.45)">${p?esc(p.date||'—'):'—'}</td>
            <td style="font-size:12px;color:rgba(255,255,255,.4);font-style:italic">${p?esc(p.feedback||'—'):'—'}</td>
          </tr>`;
        }
        tbody.innerHTML = html;
        document.getElementById('projSummary').textContent = `Total: ${total}/${max} points (${pct}% — contributes ${Math.round(pct*LMS.PROJ_WEIGHT/100*10)/10}% to final score)`;
      }

      // ── QUIZZES TABLE ─────────────────────────────────────────────────────────
      function renderQuizzesTable(quizzes, total, max, pct){
        const tbody = document.getElementById('quizzesBody');
        let html = '';
        for(let i=1;i<=LMS.QUIZZES;i++){
          const q = quizzes.find(x=>x.id==i)||null;
          const score = q ? parseFloat(q.score)||0 : 0;
          const qpct = Math.round((score/LMS.QUIZ_MAX)*100);
          html += `<tr>
            <td><strong>Quiz ${i}</strong></td>
            <td style="font-family:'Syne',sans-serif;font-weight:800">${q?score:'—'}</td>
            <td style="color:rgba(255,255,255,.4)">${LMS.QUIZ_MAX}</td>
            <td>${q?`<span class="grade ${gradeClass(qpct)}">${gradeLetter(qpct)}</span>`:'<span class="grade g-na">N/A</span>'}</td>
            <td style="font-size:12px;color:rgba(255,255,255,.45)">${q?esc(q.date||'—'):'—'}</td>
          </tr>`;
        }
        tbody.innerHTML = html;
        document.getElementById('quizSummary').textContent = `Total: ${total}/${max} points (${pct}% — contributes ${Math.round(pct*LMS.QUIZ_WEIGHT/100*10)/10}% to final score)`;
      }

      // ── BONUS TABLE ───────────────────────────────────────────────────────────
      function renderBonusTable(bonus, reason){
        const tbody = document.getElementById('bonusBody');
        if(!bonus){
          tbody.innerHTML='<tr><td class="empty" colspan="4">No bonus marks awarded yet.</td></tr>';
          return;
        }
        tbody.innerHTML = `<tr>
          <td><strong>Bonus Marks</strong></td>
          <td style="font-family:'Syne',sans-serif;font-weight:800;color:#c4b5fd">${bonus}</td>
          <td style="color:rgba(255,255,255,.4)">${LMS.BONUS_MAX}</td>
          <td style="font-size:12px;color:rgba(255,255,255,.5);font-style:italic">${esc(reason||'Awarded by instructor')}</td>
        </tr>`;
      }

      window.loadScores = loadScores;
    </script>
  </body>
</html>