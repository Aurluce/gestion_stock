/**
 * Gestion Stock - Design System JS
 * Toute l'interaction UI centralisée via data-* attributes
 */
(function () {
  'use strict';

  /* ============================
     SIDEBAR (mobile toggle + collapsible sections)
     ============================ */
  const sidebar = document.querySelector('[data-sidebar]');
  const backdrop = document.querySelector('[data-sidebar-backdrop]');
  const toggle = document.querySelector('[data-sidebar-toggle]');
  const close = document.querySelector('[data-sidebar-close]');

  function openSidebar() {
    if (!sidebar) return;
    sidebar.classList.add('open');
    if (backdrop) backdrop.classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function closeSidebar() {
    if (!sidebar) return;
    sidebar.classList.remove('open');
    if (backdrop) backdrop.classList.remove('open');
    document.body.style.overflow = '';
  }

  if (toggle) toggle.addEventListener('click', openSidebar);
  if (close) close.addEventListener('click', closeSidebar);
  if (backdrop) backdrop.addEventListener('click', closeSidebar);

  /* Collapsible sections - toggle */
  document.addEventListener('click', function(e) {
    const btn = e.target.closest('[data-toggle-section]');
    if (btn) {
      const section = btn.closest('[data-section]');
      const content = section.querySelector('.sidebar-section-content');
      const isCollapsed = content.classList.contains('collapsed');
      
      content.classList.toggle('collapsed');
      section.setAttribute('data-collapsed', !isCollapsed);
      
      // Sauvegarder l'état dans localStorage
      if (section.dataset.section) {
        localStorage.setItem('sidebar-' + section.dataset.section, !isCollapsed ? 'collapsed' : 'expanded');
      }
    }
  });

  /* Restaurer l'état des sections depuis localStorage */
  document.querySelectorAll('[data-section]').forEach(function(section) {
    const savedState = localStorage.getItem('sidebar-' + section.dataset.section);
    if (savedState === 'collapsed') {
      const content = section.querySelector('.sidebar-section-content');
      if (content) {
        content.classList.add('collapsed');
        section.setAttribute('data-collapsed', 'true');
      }
    }
  });

  /* ============================
     ACTION SHEETS (Mobile)
     ============================ */
  document.addEventListener('click', function(e) {
    // Ouvrir action sheet
    const trigger = e.target.closest('[data-action-sheet-toggle]');
    if (trigger) {
      const targetId = trigger.getAttribute('data-action-sheet-toggle');
      const sheet = document.getElementById(targetId);
      if (sheet) {
        sheet.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
      }
      e.preventDefault();
    }
    
    // Fermer action sheet
    const closeBtn = e.target.closest('[data-action-sheet-close]');
    if (closeBtn || e.target.classList.contains('action-sheet-backdrop')) {
      const sheet = closeBtn ? closeBtn.closest('.action-sheet') : e.target.closest('.action-sheet');
      if (sheet) {
        sheet.classList.add('hidden');
        document.body.style.overflow = '';
      }
    }
  });

  /* ============================
     SWIPEABLE CARDS (Touch)
     ============================ */
  let swipeStartX = 0;
  let swipeElement = null;
  
  document.addEventListener('touchstart', function(e) {
    const card = e.target.closest('[data-swipeable]');
    if (card) {
      swipeStartX = e.touches[0].clientX;
      swipeElement = card.querySelector('.swipeable-card-content');
    }
  }, { passive: true });
  
  document.addEventListener('touchmove', function(e) {
    if (swipeElement) {
      const currentX = e.touches[0].clientX;
      const diffX = currentX - swipeStartX;
      
      // Limiter le swipe à ±100px
      const clampedDiff = Math.max(-100, Math.min(100, diffX));
      swipeElement.style.transform = 'translateX(' + clampedDiff + 'px)';
    }
  }, { passive: true });
  
  document.addEventListener('touchend', function(e) {
    if (swipeElement) {
      const transform = swipeElement.style.transform;
      const translateX = parseInt(transform.replace(/[^0-9-]/g, '')) || 0;
      
      // Si swipe > 50px, laisser ouvert, sinon reset
      if (Math.abs(translateX) > 50) {
        swipeElement.style.transform = 'translateX(' + (translateX > 0 ? '100px' : '-100px') + ')';
      } else {
        swipeElement.style.transform = 'translateX(0)';
      }
      
      swipeElement = null;
    }
  });

  /* ============================
     SEGMENTED CONTROL
     ============================ */
  document.addEventListener('click', function(e) {
    const segment = e.target.closest('.segmented-control-item');
    if (segment) {
      const control = segment.closest('.segmented-control');
      control.querySelectorAll('.segmented-control-item').forEach(function(item) {
        item.classList.remove('active');
      });
      segment.classList.add('active');
      
      // Trigger event
      const index = segment.getAttribute('data-segment-index');
      const event = new CustomEvent('segmentchange', { detail: { index: parseInt(index) } });
      control.dispatchEvent(event);
    }
  });

  /* ============================
     RESPONSIVE UTILITIES
     ============================ */
  
  // Détecter si on est sur mobile
  function isMobile() {
    return window.innerWidth < 768;
  }
  
  // Détecter si on est sur tablette
  function isTablet() {
    return window.innerWidth >= 768 && window.innerWidth < 1024;
  }
  
  // Adapter l'interface au resize
  window.addEventListener('resize', function() {
    // Fermer la sidebar si on passe en desktop
    if (window.innerWidth >= 768 && sidebar && sidebar.classList.contains('open')) {
      closeSidebar();
    }
  });

  /* ============================
     VIEWPORT HEIGHT FIX (Mobile Safari)
     ============================ */
  function setVH() {
    const vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty('--vh', vh + 'px');
  }
  
  setVH();
  window.addEventListener('resize', setVH);
  window.addEventListener('orientationchange', setVH);

  /* ============================
     MODALS — data-modal-toggle, data-modal-close
     ============================ */
  document.addEventListener('click', function (e) {
    // Ouverture
    var toggleBtn = e.target.closest('[data-modal-toggle]');
    if (toggleBtn) {
      var targetId = toggleBtn.getAttribute('data-modal-toggle');
      var modal = document.getElementById(targetId);
      if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        // Focus trap
        var firstInput = modal.querySelector('input, select, textarea, button');
        if (firstInput) setTimeout(function () { firstInput.focus(); }, 100);

        // Remplir le formulaire d'édition si data-edit-user ou data-edit-group
        var json;
        if ((json = toggleBtn.getAttribute('data-edit-user'))) {
          try {
            var u = JSON.parse(json), el;
            if ((el = document.getElementById('edit_id'))) el.value = u.id_utilisateur || '';
            if ((el = document.getElementById('edit_nom'))) el.value = u.nom_complet || '';
            if ((el = document.getElementById('edit_login'))) el.value = u.login || '';
            if ((el = document.getElementById('edit_groupe'))) el.value = u.id_groupe || '';
            if ((el = document.getElementById('edit_actif'))) el.checked = u.actif == 1 || u.actif == '1';
            if ((el = document.getElementById('edit_exp'))) el.value = u.date_expiration_mdp || '';
            if ((el = document.getElementById('edit_password'))) el.value = '';
          } catch(_) {}
        } else if ((json = toggleBtn.getAttribute('data-edit-group'))) {
          try {
            var g = JSON.parse(json), el;
            if ((el = document.getElementById('edit_id'))) el.value = g.id_groupe || '';
            if ((el = document.getElementById('edit_nom'))) el.value = g.nom_groupe || '';
            if ((el = document.getElementById('edit_desc'))) el.value = g.description || '';
          } catch(_) {}
        }
      }
      e.preventDefault();
    }

    // Fermeture par bouton
    var closeBtn = e.target.closest('[data-modal-close]');
    if (closeBtn) {
      var modalEl = closeBtn.closest('.modal-overlay');
      if (modalEl) {
        modalEl.classList.add('hidden');
        modalEl.classList.remove('flex');
        document.body.style.overflow = '';
        if (modalEl.id === 'confirmModal') confirmTarget = null;
      }
      e.preventDefault();
    }

    // Fermeture par clic sur le fond
    if (e.target.classList.contains('modal-overlay')) {
      e.target.classList.add('hidden');
      e.target.classList.remove('flex');
      document.body.style.overflow = '';
      if (e.target.id === 'confirmModal') confirmTarget = null;
    }
  });

  // Fermeture modal avec Echap
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      var openModal = document.querySelector('.modal-overlay.flex');
      if (openModal) {
        openModal.classList.add('hidden');
        openModal.classList.remove('flex');
        document.body.style.overflow = '';
        if (openModal.id === 'confirmModal') confirmTarget = null;
      }
      closeSidebar();
    }
  });

  /* ============================
     TOASTS — iPhone-style auto-dismiss
     ============================ */
  function dismissToast(id) {
    var el = document.getElementById(id);
    if (!el) return;
    el.classList.add('toast-leave');
    setTimeout(function () { if (el.parentNode) el.parentNode.removeChild(el); }, 300);
  }
  document.addEventListener('DOMContentLoaded', function () {
    var toasts = document.querySelectorAll('#toast-container > .toast');
    toasts.forEach(function (toast) {
      setTimeout(function () { dismissToast(toast.id); }, 5000);
    });
  });

  /* ============================
     DETAIL MODAL — AJAX load detail content
     ============================ */
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-detail]');
    if (!btn) return;
    e.preventDefault();
    var url = btn.getAttribute('data-detail');
    var title = btn.getAttribute('data-detail-title') || 'Détails';
    var body = document.getElementById('detailBody');
    var titleEl = document.getElementById('detailModalTitle');
    var modal = document.getElementById('detailModal');
    if (!body || !modal) return;
    body.innerHTML = '<div class="flex items-center justify-center py-8"><i class="fas fa-spinner fa-spin text-h3 text-neutral-50"></i></div>';
    if (titleEl) titleEl.textContent = title;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
    fetch(url)
      .then(function(r) { return r.json(); })
      .then(function(d) { body.innerHTML = d.html; })
      .catch(function() { body.innerHTML = '<p class="text-danger-500 text-center py-8">Erreur de chargement.</p>'; });
  });

  /* ============================
     SELECT ALL checkbox
     ============================ */
  document.addEventListener('change', function (e) {
    if (e.target.id === 'selectAll' || e.target.getAttribute('data-select-all') !== null) {
      var name = e.target.getAttribute('data-select-name') || 'droits[]';
      var checkboxes = document.querySelectorAll('input[name="' + name + '"]');
      checkboxes.forEach(function (cb) { cb.checked = e.target.checked; });
    }
  });

  /* ============================
     CONFIRM — data-confirm (custom modal)
     ============================ */
  var confirmTarget = null;
  document.addEventListener('click', function (e) {
    var el = e.target.closest('[data-confirm]');
    if (el) {
      e.preventDefault();
      var message = el.getAttribute('data-confirm') || 'Confirmez-vous cette action ?';
      var confirmType = el.getAttribute('data-confirm-type') || 'danger';
      var confirmModal = document.getElementById('confirmModal');
      var confirmMsg = document.getElementById('confirmMessage');
      var confirmLink = document.getElementById('confirmLink');
      var confirmIcon = document.getElementById('confirmModalIcon');
      var linkIcon = document.getElementById('confirmLinkIcon');
      var linkLabel = document.getElementById('confirmLinkLabel');
      if (!confirmModal || !confirmMsg || !confirmLink) return;
      confirmMsg.textContent = message;
      // Style selon le type
      var typeConfig = {
        danger:  { icon: 'fa-exclamation-triangle', btnClass: 'btn-danger',  btnIcon: 'fa-trash', label: 'Supprimer' },
        warning: { icon: 'fa-exclamation-circle',   btnClass: 'btn-warning', btnIcon: 'fa-ban',   label: 'Annuler' },
        success: { icon: 'fa-check-circle',          btnClass: 'btn-success', btnIcon: 'fa-check', label: 'Valider' }
      };
      var cfg = typeConfig[confirmType] || typeConfig.danger;
      if (confirmIcon) confirmIcon.className = 'fas ' + cfg.icon;
      if (linkIcon) linkIcon.className = 'fas ' + cfg.btnIcon;
      if (linkLabel) linkLabel.textContent = cfg.label;
      confirmLink.className = cfg.btnClass;
      var href = el.getAttribute('href');
      if (href) {
        confirmTarget = { href: href, form: null };
        confirmLink.setAttribute('href', href);
        confirmLink.style.display = 'inline-flex';
      } else {
        var form = el.closest('form');
        if (form) {
          confirmTarget = { href: null, form: form };
          confirmLink.setAttribute('href', '#');
          confirmLink.style.display = 'inline-flex';
        } else {
          confirmTarget = null;
          confirmLink.style.display = 'none';
        }
      }
      confirmModal.classList.remove('hidden');
      confirmModal.classList.add('flex');
      document.body.style.overflow = 'hidden';
    }
  });
  document.getElementById('confirmLink').addEventListener('click', function (e) {
    if (confirmTarget && confirmTarget.form) {
      e.preventDefault();
      confirmTarget.form.submit();
    }
  });

  /* ============================
     DROPDOWN — data-dropdown-toggle
     ============================ */
  document.addEventListener('click', function (e) {
    var dropdownBtn = e.target.closest('[data-dropdown-toggle]');
    if (dropdownBtn) {
      var targetId2 = dropdownBtn.getAttribute('data-dropdown-toggle');
      var dropdown = document.getElementById(targetId2);
      if (dropdown) {
        var isHidden = dropdown.classList.contains('hidden');
        document.querySelectorAll('.dropdown').forEach(function (d) {
          d.classList.add('hidden');
        });
        if (isHidden) {
          dropdown.classList.remove('hidden');
        }
      }
      e.stopPropagation();
    } else {
      document.querySelectorAll('.dropdown').forEach(function (d) {
        d.classList.add('hidden');
      });
    }
  });

  /* ============================
     SIDEBAR ACTIVE ITEM (highlight)
     ============================ */
  document.querySelectorAll('.sidebar-item').forEach(function (item) {
    if (item.classList.contains('active')) {
      item.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    }
  });

  /* ============================
     FILTER BAR toggle (mobile)
     ============================ */
  document.addEventListener('click', function (e) {
    var toggle = e.target.closest('[data-filter-toggle]');
    if (toggle) {
      var filterId = toggle.getAttribute('data-filter-toggle');
      var filterBar = document.getElementById(filterId);
      if (filterBar) {
        filterBar.classList.toggle('filter-bar-collapsed');
        // Changer icône
        var icon = toggle.querySelector('i');
        if (icon) {
          icon.classList.toggle('fa-sliders-h');
          icon.classList.toggle('fa-times');
        }
      }
      e.preventDefault();
    }
  });

  /* ============================
     SELECT ALL / DESELECT ALL (Droits)
     ============================ */
  var selectAllCheckbox = document.getElementById('selectAll');
  if (selectAllCheckbox) {
    var droitCheckboxes = document.querySelectorAll('input[name="droits[]"]');
    
    // Gérer le clic sur "Tout cocher / décocher"
    selectAllCheckbox.addEventListener('change', function() {
      droitCheckboxes.forEach(function(checkbox) {
        checkbox.checked = selectAllCheckbox.checked;
      });
    });
    
    // Mettre à jour l'état du "selectAll" en fonction des checkboxes individuelles
    droitCheckboxes.forEach(function(checkbox) {
      checkbox.addEventListener('change', function() {
        var allChecked = Array.from(droitCheckboxes).every(function(cb) {
          return cb.checked;
        });
        var someChecked = Array.from(droitCheckboxes).some(function(cb) {
          return cb.checked;
        });
        
        selectAllCheckbox.checked = allChecked;
        selectAllCheckbox.indeterminate = someChecked && !allChecked;
      });
    });
    
    // Initialiser l'état du selectAll si certains droits sont déjà sélectionnés
    var allChecked = Array.from(droitCheckboxes).every(function(cb) {
      return cb.checked;
    });
    var someChecked = Array.from(droitCheckboxes).some(function(cb) {
      return cb.checked;
    });
    selectAllCheckbox.checked = allChecked;
    selectAllCheckbox.indeterminate = someChecked && !allChecked;
  }
})();
