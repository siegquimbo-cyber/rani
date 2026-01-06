document.addEventListener("DOMContentLoaded", () => {
  const iframe = document.getElementById("preview-frame");
  const previewBtn = document.getElementById("preview-btn");
  const editBtn = document.getElementById("edit-btn");
  const saveBtn = document.getElementById("save-btn");
  const serializedField = document.getElementById("html_content");

  let isEditMode = false;

  if (!iframe) return;

  iframe.addEventListener("load", () => {
    const doc = iframe.contentDocument || iframe.contentWindow.document;

    // Highlight editable elements on hover for clarity
    doc.body.addEventListener("mouseover", (e) => {
      const el = e.target;
      if (!isEditMode) return;
      if (el.tagName !== "IMG") {
        el.style.outline = "1px dashed #ff4f4f";
      }
    });
    doc.body.addEventListener("mouseout", (e) => {
      const el = e.target;
      if (el.tagName !== "IMG") {
        el.style.outline = "none";
      }
    });

    // Make text nodes editable on double-click
    doc.body.addEventListener("dblclick", (e) => {
      const el = e.target;
      if (!isEditMode) return;
      if (el.tagName !== "IMG") {
        el.setAttribute("contenteditable", "true");
        el.focus();
      }
    });

    // Prevent navigation and other link actions while in edit mode
    // Block all click interactions in edit mode to avoid triggering site JS (e.g., cart pop-ups)
    doc.addEventListener(
      "click",
      (e) => {
        if (!isEditMode) return;
        e.preventDefault();
        e.stopPropagation();
        if (e.stopImmediatePropagation) e.stopImmediatePropagation();
      },
      true // capture phase to intercept early
    );
  });

  // Mode switching buttons
  if (previewBtn) {
    previewBtn.addEventListener("click", () => {
      isEditMode = false;
      previewBtn.classList.add("active");
      if (editBtn) editBtn.classList.remove("active");
      iframe.src = iframe.src.split("?")[0] + "?t=" + Date.now();
    });
  }
  if (editBtn) {
    editBtn.addEventListener("click", () => {
      isEditMode = true;
      editBtn.classList.add("active");
      if (previewBtn) previewBtn.classList.remove("active");
      iframe.src = iframe.src.split("?")[0] + "?t=" + Date.now();
    });
  }

  // Serialize the iframe DOM & submit
  if (saveBtn) {
    saveBtn.addEventListener("click", (e) => {
      e.preventDefault();
      const doc = iframe.contentDocument || iframe.contentWindow.document;
      serializedField.value = "<!DOCTYPE html>\n" + doc.documentElement.outerHTML;
      serializedField.form.submit();
    });
  }
});
