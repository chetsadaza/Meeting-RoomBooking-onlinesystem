document.addEventListener("DOMContentLoaded", () => {
  const settingsMenu = document.getElementById("settingsMenu");
  const submenu = settingsMenu ? settingsMenu.querySelector(".navbar__submenu") : null;

  // Toggle submenu on click (if submenu exists)
  if (submenu) {
    settingsMenu.addEventListener("click", (e) => {
      e.preventDefault();
      settingsMenu.classList.toggle("active");
    });

    // Close submenu when clicking outside
    document.addEventListener("click", (e) => {
      if (!settingsMenu.contains(e.target)) {
        settingsMenu.classList.remove("active");
      }
    });
  }
});


document.addEventListener("DOMContentLoaded", () => {
  feather.replace(); // ใช้ Feather Icons
});
