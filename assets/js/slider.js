(function () {
  "use strict";

  function init(root) {
    if (root.dataset.mtsReady === "1") return;
    root.dataset.mtsReady = "1";

    const viewport = root.querySelector(".mts-viewport");
    const track = root.querySelector(".mts-track");
    if (!viewport || !track) return;

    const originalSlides = [...track.children].filter(el => !el.classList.contains("mts-clone"));
    const count = originalSlides.length;
    if (!count) return;

    let cfg = {};
    try { cfg = JSON.parse(root.dataset.config || "{}"); } catch (e) {}

    const loop = cfg.loop !== false && count > 1;
    const speed = Math.max(100, Number(cfg.speed) || 550);
    const allowedAnimations = ["slide", "fade", "fade-up", "zoom", "none"];
    const animation = allowedAnimations.includes(cfg.animation) ? cfg.animation : "slide";
    const easing = cfg.easing || "ease";

    root.dataset.animation = animation;
    root.style.setProperty("--mts-duration", speed + "ms");
    root.style.setProperty("--mts-easing", easing);

    const reduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    const prevButtons = root.querySelectorAll(".mts-prev");
    const nextButtons = root.querySelectorAll(".mts-next");
    const dotsContainer = root.querySelector(".mts-dots");
    const dotButtons = [];

    // Setup Dots
    if (dotsContainer) {
      dotsContainer.innerHTML = "";
      if (count > 1) {
        for (let i = 0; i < count; i++) {
          const b = document.createElement("button");
          b.type = "button";
          b.className = "mts-dot" + (i === 0 ? " is-active" : "");
          b.setAttribute("aria-label", "Go to testimonial " + (i + 1));
          b.addEventListener("click", () => goToRealIndex(i));
          dotsContainer.appendChild(b);
          dotButtons.push(b);
        }
      }
    }

    let realIndex = 0;
    let trackIndex = 0;
    let isTransitioning = false;
    let timer = null;

    const isSlideMode = (animation === "slide");
    const useClones = isSlideMode && loop && count > 1;

    // Clone setup for seamless infinite loop (Slide mode only)
    if (useClones) {
      track.querySelectorAll(".mts-clone").forEach(el => el.remove());

      const firstClone = originalSlides[0].cloneNode(true);
      firstClone.classList.add("mts-clone");
      firstClone.setAttribute("aria-hidden", "true");

      const lastClone = originalSlides[count - 1].cloneNode(true);
      lastClone.classList.add("mts-clone");
      lastClone.setAttribute("aria-hidden", "true");

      track.insertBefore(lastClone, originalSlides[0]);
      track.appendChild(firstClone);

      trackIndex = 1; // Real slide 0 is at track index 1
      realIndex = 0;
    } else {
      trackIndex = 0;
      realIndex = 0;
    }

    function setTrackPosition(idx, animate = false) {
      const duration = (animate && !reduced && animation !== "none") ? speed : 0;
      track.style.transitionDuration = duration + "ms";
      track.style.transitionTimingFunction = easing;
      track.style.transform = isSlideMode ? "translate3d(-" + (idx * 100) + "%, 0, 0)" : "none";
    }

    function updateActiveStates() {
      originalSlides.forEach((slide, i) => {
        const isActive = (i === realIndex);
        slide.classList.toggle("mts-active", isActive);
        slide.setAttribute("aria-hidden", isActive ? "false" : "true");
        slide.querySelectorAll("a, button, input").forEach(el => {
          if (isActive) el.removeAttribute("tabindex");
          else el.setAttribute("tabindex", "-1");
        });
      });

      dotButtons.forEach((b, i) => {
        const isActive = (i === realIndex);
        b.classList.toggle("is-active", isActive);
        b.setAttribute("aria-current", isActive ? "true" : "false");
      });

      // Update arrow disabled states when loop is disabled
      if (!loop) {
        prevButtons.forEach(btn => btn.disabled = (realIndex === 0));
        nextButtons.forEach(btn => btn.disabled = (realIndex === count - 1));
      } else {
        prevButtons.forEach(btn => btn.disabled = false);
        nextButtons.forEach(btn => btn.disabled = false);
      }
    }

    // Seamless jump after animating into a clone
    track.addEventListener("transitionend", function (e) {
      if (e.target !== track || !useClones) {
        isTransitioning = false;
        return;
      }

      if (trackIndex === count + 1) {
        // Advanced forward into firstClone -> seamlessly jump to real slide 0
        trackIndex = 1;
        setTrackPosition(trackIndex, false);
      } else if (trackIndex === 0) {
        // Advanced backward into lastClone -> seamlessly jump to real slide N-1
        trackIndex = count;
        setTrackPosition(trackIndex, false);
      }
      isTransitioning = false;
    });

    function nextSlide() {
      if (count < 2) return;
      if (isTransitioning) return;

      if (useClones) {
        isTransitioning = true;
        trackIndex++;
        realIndex = (realIndex + 1) % count;
        setTrackPosition(trackIndex, true);
        updateActiveStates();
      } else {
        if (!loop && realIndex >= count - 1) return;
        realIndex = loop ? (realIndex + 1) % count : Math.min(count - 1, realIndex + 1);
        trackIndex = realIndex;
        setTrackPosition(trackIndex, true);
        updateActiveStates();
      }
      restart();
    }

    function prevSlide() {
      if (count < 2) return;
      if (isTransitioning) return;

      if (useClones) {
        isTransitioning = true;
        trackIndex--;
        realIndex = (realIndex - 1 + count) % count;
        setTrackPosition(trackIndex, true);
        updateActiveStates();
      } else {
        if (!loop && realIndex <= 0) return;
        realIndex = loop ? (realIndex - 1 + count) % count : Math.max(0, realIndex - 1);
        trackIndex = realIndex;
        setTrackPosition(trackIndex, true);
        updateActiveStates();
      }
      restart();
    }

    function goToRealIndex(targetIndex) {
      if (count < 2 || targetIndex === realIndex) return;
      if (isTransitioning) return;

      realIndex = targetIndex;
      trackIndex = useClones ? (targetIndex + 1) : targetIndex;
      setTrackPosition(trackIndex, true);
      updateActiveStates();
      restart();
    }

    function stop() {
      if (timer) {
        clearInterval(timer);
        timer = null;
      }
    }

    function start() {
      stop();
      if (cfg.autoplay && !reduced && count > 1) {
        timer = setInterval(() => {
          if (!loop && realIndex >= count - 1) {
            stop();
            return;
          }
          nextSlide();
        }, Math.max(500, Number(cfg.delay) || 4500));
      }
    }

    function restart() {
      if (!root.matches(":hover") || !cfg.hover) start();
    }

    // Bind event listeners
    prevButtons.forEach(btn => btn.addEventListener("click", prevSlide));
    nextButtons.forEach(btn => btn.addEventListener("click", nextSlide));

    if (cfg.hover) {
      root.addEventListener("mouseenter", stop);
      root.addEventListener("mouseleave", start);
    }
    if (cfg.focus) {
      root.addEventListener("focusin", stop);
      root.addEventListener("focusout", e => {
        if (!root.contains(e.relatedTarget)) start();
      });
    }

    if (cfg.keyboard) {
      root.setAttribute("tabindex", "0");
      root.addEventListener("keydown", e => {
        if (e.key === "ArrowLeft") {
          e.preventDefault();
          prevSlide();
        } else if (e.key === "ArrowRight") {
          e.preventDefault();
          nextSlide();
        }
      });
    }

    // Touch Swipe Support
    if (cfg.swipe && count > 1) {
      let startX = 0;
      let currentX = 0;
      let isSwiping = false;

      viewport.addEventListener("touchstart", e => {
        if (isTransitioning) return;
        stop();
        isSwiping = true;
        startX = e.changedTouches[0].clientX;
        currentX = startX;
      }, { passive: true });

      viewport.addEventListener("touchmove", e => {
        if (!isSwiping) return;
        currentX = e.changedTouches[0].clientX;
      }, { passive: true });

      viewport.addEventListener("touchend", () => {
        if (!isSwiping) return;
        isSwiping = false;
        const diff = currentX - startX;
        if (Math.abs(diff) > 40) {
          if (diff < 0) nextSlide();
          else prevSlide();
        } else {
          start();
        }
      }, { passive: true });

      viewport.addEventListener("touchcancel", () => {
        isSwiping = false;
        start();
      }, { passive: true });
    }

    document.addEventListener("visibilitychange", () => {
      if (document.hidden) stop();
      else start();
    });

    function syncLayout() {
      originalSlides.forEach(slide => {
        const dir = getComputedStyle(slide).flexDirection;
        slide.classList.toggle("is-stacked", dir === "column" || dir === "column-reverse");
      });
    }
    window.addEventListener("resize", syncLayout, { passive: true });
    if (window.ResizeObserver) {
      const observer = new ResizeObserver(syncLayout);
      observer.observe(root);
    }

    // Initial paint
    syncLayout();
    setTrackPosition(trackIndex, false);
    updateActiveStates();
    start();
  }

  function scan(scope = document) {
    scope.querySelectorAll(".mts-root").forEach(init);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => scan());
  } else {
    scan();
  }

  window.addEventListener("elementor/frontend/init", function () {
    if (window.elementorFrontend && elementorFrontend.hooks) {
      elementorFrontend.hooks.addAction("frontend/element_ready/testimonial_slider.default", function ($scope) {
        const el = $scope[0] ? $scope[0].querySelector(".mts-root") : null;
        if (el) {
          el.dataset.mtsReady = "0"; // Force re-init in Elementor preview
          init(el);
        }
      });
    }
  });
})();
