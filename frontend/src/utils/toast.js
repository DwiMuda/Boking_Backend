let toastContainer = null
let toastId = 0

const ICONS = {
  success: '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>',
  error: '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>',
  warning: '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>',
  info: '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>'
}

function getContainer() {
  if (!toastContainer) {
    toastContainer = document.createElement('div')
    toastContainer.id = 'toast-container'
    toastContainer.style.cssText = `
      position:fixed;top:1rem;right:1rem;z-index:600;
      display:flex;flex-direction:column;gap:0.5rem;
      pointer-events:none;max-width:400px;width:100%;
    `
    document.body.appendChild(toastContainer)
  }
  return toastContainer
}

const COLORS = {
  success: { bg: 'rgba(16,185,129,0.12)', border: 'rgba(16,185,129,0.25)', text: '#10B981' },
  error: { bg: 'rgba(239,68,68,0.12)', border: 'rgba(239,68,68,0.25)', text: '#EF4444' },
  warning: { bg: 'rgba(245,158,11,0.12)', border: 'rgba(245,158,11,0.25)', text: '#F59E0B' },
  info: { bg: 'rgba(59,130,246,0.12)', border: 'rgba(59,130,246,0.25)', text: '#3B82F6' }
}

export function showToast(message, type = 'info', duration = 3500) {
  const container = getContainer()
  const id = ++toastId
  const colors = COLORS[type] || COLORS.info

  const el = document.createElement('div')
  el.id = `toast-${id}`
  el.style.cssText = `
    pointer-events:auto;
    display:flex;align-items:flex-start;gap:0.75rem;
    padding:0.85rem 1rem;
    background:${colors.bg};
    border:1px solid ${colors.border};
    border-radius:12px;
    color:${colors.text};
    font-size:0.875rem;
    font-family:'Manrope',system-ui,sans-serif;
    line-height:1.4;
    box-shadow:0 8px 32px rgba(0,0,0,0.4);
    backdrop-filter:blur(12px);
    transform:translateX(calc(100% + 2rem));
    opacity:0;
    transition:all 0.35s cubic-bezier(0.4,0,0.2,1);
    position:relative;
    overflow:hidden;
  `

  el.innerHTML = `
    <span style="flex-shrink:0;display:flex;margin-top:1px">${ICONS[type] || ICONS.info}</span>
    <span style="flex:1;color:var(--text-primary)">${message}</span>
    <button onclick="this.closest('[id^=toast-]').click()" style="
      flex-shrink:0;background:none;border:none;cursor:pointer;
      color:var(--text-muted);padding:0;display:flex;font-size:1rem;
      transition:color 0.15s;line-height:1
    " onmouseover="this.style.color='var(--text-primary)'" onmouseout="this.style.color=''" aria-label="Tutup">&times;</button>
    <div style="
      position:absolute;bottom:0;left:0;height:3px;
      background:${colors.text};border-radius:0 3px 3px 0;
      transition:width ${duration}ms linear;width:100%;
    " class="toast-progress"></div>
  `

  container.appendChild(el)

  requestAnimationFrame(() => {
    el.style.transform = 'translateX(0)'
    el.style.opacity = '1'
  })

  const dismiss = () => {
    el.style.transform = 'translateX(calc(100% + 2rem))'
    el.style.opacity = '0'
    setTimeout(() => el.remove(), 350)
  }

  const timer = setTimeout(dismiss, duration)

  const progress = el.querySelector('.toast-progress')
  requestAnimationFrame(() => {
    progress.style.width = '0%'
  })

  el.addEventListener('click', (e) => {
    if (e.target.closest('button')) return
    clearTimeout(timer)
    dismiss()
  })

  el.querySelector('button').addEventListener('click', (e) => {
    e.stopPropagation()
    clearTimeout(timer)
    dismiss()
  })

  return { id, dismiss }
}

export function installToast(app) {
  app.config.globalProperties.$toast = showToast
}
