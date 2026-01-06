document.addEventListener("DOMContentLoaded", () => {
  const iframe = document.getElementById("preview-frame");
  const fileInput = document.getElementById("hidden-file");
  const modal = document.getElementById("img-choice-modal");
  const choiceButtons = modal ? modal.querySelectorAll("[data-choice]") : [];
  const cancelChoice = document.getElementById("choice-cancel");
  const previewBtn = document.getElementById("preview-btn");
  const editBtn = document.getElementById("edit-btn");
  const saveBtn = document.getElementById("save-refresh-btn");
  const modeLabel = document.getElementById("mode-label");

  let currentImg = null;
  let isEditMode = false; // start in preview mode

  if (!iframe || !fileInput) return;

  function attachEditHandlers(doc) {
    doc.querySelectorAll("img").forEach((img) => {
      // highlight on hover
      const over = () => {
        if (!isEditMode) return;
        img.style.outline = "2px solid #18f1e1";
        img.style.cursor = "pointer";
      };
      const out = () => {
        img.style.outline = "none";
      };
      const click = (e) => {
        if (!isEditMode) return;
        e.preventDefault();
        e.stopPropagation();
        if (e.stopImmediatePropagation) e.stopImmediatePropagation();
        currentImg = img;
        // Show choice modal first
        if (modal) modal.style.display = "flex";
      };

      img.addEventListener("mouseover", over);
      img.addEventListener("mouseout", out);
      img.addEventListener("click", click);
    });
  }

  function reloadIframe() {
    iframe.src = iframe.src.split("?")[0] + "?t=" + Date.now();
  }

  iframe.addEventListener("load", () => {
    const doc = iframe.contentDocument || iframe.contentWindow.document;
    attachEditHandlers(doc);
  });

  // Mode buttons
  if (previewBtn) {
    previewBtn.addEventListener("click", () => {
      isEditMode = false;
      if (modeLabel) modeLabel.textContent = "Preview Mode";
      previewBtn.classList.add("active");
      editBtn.classList.remove("active");
      reloadIframe();
    });
  }

  if (editBtn) {
    editBtn.addEventListener("click", () => {
      isEditMode = true;
      if (modeLabel) modeLabel.textContent = "Edit Mode";
      editBtn.classList.add("active");
      previewBtn.classList.remove("active");
      reloadIframe();
    });
  }

  if (saveBtn) {
    saveBtn.addEventListener("click", () => {
      alert("Changes saved (images are updated as soon as you upload). Reloading preview…");
      reloadIframe();
    });
  }

  // Handle choice modal selections
  choiceButtons.forEach((btn) => {
    btn.addEventListener("click", () => {
      if (!currentImg) return;
      const type = btn.getAttribute("data-choice"); // main | hover
      let targetImg = currentImg;
      if (type === "main") {
        // first img in same parent
        const first = currentImg.parentElement.querySelector("img");
        if (first) targetImg = first;
      } else if (type === "hover") {
        const imgs = currentImg.parentElement.querySelectorAll("img");
        if (imgs.length > 1) targetImg = imgs[1];
      }
      currentImg = targetImg;
      if (modal) modal.style.display = "none";
      fileInput.click();
    });
  });

  if (cancelChoice) cancelChoice.addEventListener("click", () => {
    if (modal) modal.style.display = "none";
    currentImg = null;
  });

  // Handle actual file upload
  fileInput.addEventListener("change", () => {
    if (!currentImg || !fileInput.files.length) return;
    if (modal) modal.style.display = "none";

    // Derive path relative to baseDir (assets/images/...)
    // Take clean src without cache param
    let src = currentImg.getAttribute("src").split("?")[0];
    src = src.replace(/^\.\.\//, "");
    src = src.replace(/^assets\/?images\//, "");

    const formData = new FormData();
    formData.append("target_path", src);
    formData.append("new_image", fileInput.files[0]);

    fetch("edit_media.php", {
      method: "POST",
      body: formData,
    })
      .then((resp) => resp.text())
      .then((text) => {
        if (text.includes("Image replaced successfully")) {
        // Bust cache for the edited image only if still in edit mode
        if (isEditMode) {
          const origSrc = currentImg.getAttribute("src").split("?")[0];
          currentImg.src = origSrc + "?t=" + Date.now();
        }
          alert("Image replaced successfully!");
        } else {
          alert("Upload completed, but server did not confirm replacement. Check file permissions.");
        }
      })
      .catch(() => alert("Failed to upload image."))
      .finally(() => {
        // Reset input and refresh preview
        fileInput.value = "";
        reloadIframe();
      });
  });
});