document.addEventListener("DOMContentLoaded", () => {
  const dropdowns = document.querySelectorAll(".dropdown");

  dropdowns.forEach(drop => {
    const toggle = drop.querySelector(".dropdown-toggle");
    const menu = drop.querySelector(".dropdown-content");

    toggle.addEventListener("click", e => {
      e.preventDefault();
      drop.classList.toggle("active");

      if (drop.classList.contains("active")) {
        menu.style.opacity = "1";
        menu.style.visibility = "visible";
        menu.style.transform = "translateX(-50%) translateY(3px)";
      } else {
        menu.style.opacity = "0";
        menu.style.visibility = "hidden";
        menu.style.transform = "translateX(-50%) translateY(0)";
      }
    });
  });

  document.addEventListener("click", e => {
    if (!e.target.closest(".dropdown")) {
      document.querySelectorAll(".dropdown").forEach(drop => {
        drop.classList.remove("active");
        const menu = drop.querySelector(".dropdown-content");
        menu.style.opacity = "0";
        menu.style.visibility = "hidden";
        menu.style.transform = "translateX(-50%) translateY(0)";
      });
    }
  });
});