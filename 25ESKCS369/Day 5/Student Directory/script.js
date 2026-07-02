 $(document).ready(function () {
 
  const students = [
    { id: "CSE101", name: "Rahul Sharma",     branch: "CSE",   cgpa: 9.1, email: "rahul.sharma@college.edu",   phone: "+91 98765 43210", sem: "6th", address: "Jaipur, Rajasthan" },
    { id: "CSE102", name: "Ananya Verma",     branch: "CSE",   cgpa: 9.6, email: "ananya.verma@college.edu",   phone: "+91 98220 11223", sem: "6th", address: "Udaipur, Rajasthan" },
    { id: "CSE103", name: "Karan Mehta",      branch: "CSE",   cgpa: 8.3, email: "karan.mehta@college.edu",    phone: "+91 90123 44556", sem: "4th", address: "Ahmedabad, Gujarat" },
    { id: "IT201",  name: "Priya Nair",       branch: "IT",    cgpa: 8.9, email: "priya.nair@college.edu",     phone: "+91 99887 66554", sem: "6th", address: "Kochi, Kerala" },
    { id: "IT202",  name: "Aditya Singh",     branch: "IT",    cgpa: 7.6, email: "aditya.singh@college.edu",   phone: "+91 91234 55667", sem: "4th", address: "Lucknow, UP" },
    { id: "IT203",  name: "Sneha Kulkarni",   branch: "IT",    cgpa: 9.3, email: "sneha.kulkarni@college.edu", phone: "+91 98700 12345", sem: "6th", address: "Pune, Maharashtra" },
    { id: "ECE301", name: "Vikram Rathore",   branch: "ECE",   cgpa: 8.1, email: "vikram.rathore@college.edu", phone: "+91 97654 32109", sem: "4th", address: "Jodhpur, Rajasthan" },
    { id: "ECE302", name: "Ishita Malhotra",  branch: "ECE",   cgpa: 7.4, email: "ishita.malhotra@college.edu",phone: "+91 96543 21098", sem: "2nd", address: "Chandigarh" },
    { id: "ECE303", name: "Devansh Joshi",    branch: "ECE",   cgpa: 8.8, email: "devansh.joshi@college.edu",  phone: "+91 95432 10987", sem: "6th", address: "Indore, MP" },
    { id: "ME401",  name: "Ritika Bansal",    branch: "ME",    cgpa: 7.9, email: "ritika.bansal@college.edu",  phone: "+91 94321 09876", sem: "4th", address: "Kanpur, UP" },
    { id: "ME402",  name: "Arjun Chauhan",    branch: "ME",    cgpa: 6.9, email: "arjun.chauhan@college.edu",  phone: "+91 93210 98765", sem: "2nd", address: "Bhopal, MP" },
    { id: "ME403",  name: "Simran Kaur",      branch: "ME",    cgpa: 8.5, email: "simran.kaur@college.edu",    phone: "+91 92109 87654", sem: "6th", address: "Amritsar, Punjab" },
    { id: "CIV501", name: "Manav Pillai",     branch: "Civil", cgpa: 7.2, email: "manav.pillai@college.edu",   phone: "+91 91098 76543", sem: "4th", address: "Chennai, Tamil Nadu" },
    { id: "CIV502", name: "Tanvi Deshmukh",   branch: "Civil", cgpa: 8.0, email: "tanvi.deshmukh@college.edu", phone: "+91 90987 65432", sem: "2nd", address: "Nagpur, Maharashtra" },
    { id: "CIV503", name: "Yash Agarwal",     branch: "Civil", cgpa: 9.0, email: "yash.agarwal@college.edu",   phone: "+91 89876 54321", sem: "6th", address: "Jaipur, Rajasthan" }
  ];

   
  const branchColors = {
    CSE:   { solid: "#5B5FEF", soft: "rgba(91, 95, 239, 0.14)" },
    IT:    { solid: "#00A99D", soft: "rgba(0, 169, 157, 0.14)" },
    ECE:   { solid: "#FF9F45", soft: "rgba(255, 159, 69, 0.16)" },
    ME:    { solid: "#FF5C7A", soft: "rgba(255, 92, 122, 0.14)" },
    Civil: { solid: "#C98F1F", soft: "rgba(201, 143, 31, 0.16)" }
  };

  let workingData = students.slice();


  function getInitials(name) {
    const parts = name.trim().split(" ");
    return (parts[0][0] + (parts[1] ? parts[1][0] : "")).toUpperCase();
  }

  function buildCard(student) {
    const color = branchColors[student.branch] || branchColors.CSE;

    return `
      <div class="col-12 col-sm-6 col-lg-4 col-xl-3 student-col"
           data-name="${student.name.toLowerCase()}"
           data-id="${student.id.toLowerCase()}"
           data-branch="${student.branch}"
           data-cgpa="${student.cgpa}">
        <div class="student-card" style="--branch-color:${color.solid}; --branch-color-soft:${color.soft};">

          <div class="card-top">
            <div class="d-flex gap-2 align-items-center">
              <div class="avatar-circle">${getInitials(student.name)}</div>
              <div>
                <p class="student-name mb-0">${student.name}</p>
                <p class="student-id mb-0">${student.id}</p>
              </div>
            </div>
            <span class="branch-badge">${student.branch}</span>
          </div>

          <div class="cgpa-row">
            <i class="bi bi-star-fill" style="color:var(--branch-color);"></i>
            <span class="cgpa-value">${student.cgpa.toFixed(1)}</span>
            <span class="cgpa-label">CGPA</span>
          </div>

          <button class="btn details-toggle-btn">
            Show Details <i class="bi bi-chevron-down"></i>
          </button>

          <div class="details-panel">
            <div class="detail-row"><i class="bi bi-envelope-fill"></i><span class="val">${student.email}</span></div>
            <div class="detail-row"><i class="bi bi-telephone-fill"></i><span class="val">${student.phone}</span></div>
            <div class="detail-row"><i class="bi bi-journal-bookmark-fill"></i><span class="val">Semester: ${student.sem}</span></div>
            <div class="detail-row"><i class="bi bi-geo-alt-fill"></i><span class="val">${student.address}</span></div>
          </div>

        </div>
      </div>
    `;
  }

  function renderCards(data) {
    const $container = $("#cardContainer");
    $container.empty();
    data.forEach(function (student) {
      $container.append(buildCard(student));
    });
  }

 
  function updateStaticStats() {
    const total = students.length;
    const highest = Math.max(...students.map(s => s.cgpa)).toFixed(1);
    const branchCount = new Set(students.map(s => s.branch)).size;

    $("#statTotal").text(total);
    $("#statHighest").text(highest);
    $("#statBranches").text(branchCount);
  }

  function updateVisibleStat() {
    const visibleCount = $(".student-col:visible").length;
    $("#statVisible").text(visibleCount);
  }

  
  function applyFilters() {
    const keyword = $("#searchInput").val().trim().toLowerCase();
    const branch = $("#branchFilter").val();
    const minCgpa = parseFloat($("#cgpaFilter").val());

    let anyVisible = false;

    $(".student-col").each(function () {
      const $card = $(this);
      const name = $card.data("name").toString();
      const id = $card.data("id").toString();
      const cardBranch = $card.data("branch").toString();
      const cgpa = parseFloat($card.data("cgpa"));

      const matchesSearch =
        keyword === "" ||
        name.includes(keyword) ||
        id.includes(keyword) ||
        cardBranch.toLowerCase().includes(keyword) ||
        cgpa.toString().includes(keyword);

      const matchesBranch = branch === "all" || cardBranch === branch;
      const matchesCgpa = cgpa >= minCgpa;

      if (matchesSearch && matchesBranch && matchesCgpa) {
        $card.fadeIn(200);
        anyVisible = true;
      } else {
        $card.fadeOut(150);
      }
    });

    
    setTimeout(function () {
      if (anyVisible) {
        $("#noResult").addClass("d-none");
      } else {
        $("#noResult").removeClass("d-none");
      }
      updateVisibleStat();
    }, 210);
  }

 
  let sortedAsc = true;
  $("#sortBtn").on("click", function () {
    workingData.sort(function (a, b) {
      return sortedAsc
        ? a.name.localeCompare(b.name)
        : b.name.localeCompare(a.name);
    });
    sortedAsc = !sortedAsc;

    const $container = $("#cardContainer");
    $container.fadeOut(180, function () {
      renderCards(workingData);
      $container.fadeIn(220);
      applyFilters();
    });
  });

  
  $("#cardContainer").on("click", ".details-toggle-btn", function () {
    const $btn = $(this);
    const $panel = $btn.siblings(".details-panel");

    $panel.slideToggle(280, function () {
      const isOpen = $panel.is(":visible");
      $btn.html(
        isOpen
          ? 'Hide Details <i class="bi bi-chevron-up"></i>'
          : 'Show Details <i class="bi bi-chevron-down"></i>'
      );
    });
  });

  $("#themeToggle").on("click", function () {
    $("body").toggleClass("dark-mode");
    const isDark = $("body").hasClass("dark-mode");
    $("#themeIcon")
      .toggleClass("bi-moon-stars-fill", !isDark)
      .toggleClass("bi-brightness-high-fill", isDark);
  });

  
  $("#searchInput").on("keyup", applyFilters);
  $("#branchFilter").on("change", applyFilters);
  $("#cgpaFilter").on("change", applyFilters);

  $("#resetBtn").on("click", function () {
    $("#searchInput").val("");
    $("#branchFilter").val("all");
    $("#cgpaFilter").val("0");
    sortedAsc = true;
    workingData = students.slice();
    renderCards(workingData);
    applyFilters();
  });

  $(window).on("scroll", function () {
    $("#mainNavbar").toggleClass("scrolled", $(window).scrollTop() > 10);
  });

 
  renderCards(workingData);
  updateStaticStats();
  updateVisibleStat();
});