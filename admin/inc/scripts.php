<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<script>
  const nativeAlert = window.alert ? window.alert.bind(window) : function(){};

  function getAlertHost(position='body'){
    if(position !== 'body'){
      const target = document.getElementById(position);
      if(target) return target;
    }

    let host = document.getElementById('global-alert-host');
    if(!host){
      host = document.createElement('div');
      host.id = 'global-alert-host';
      host.className = 'custom-alert-host';
      host.style.position = 'fixed';
      host.style.top = '84px';
      host.style.right = '20px';
      host.style.zIndex = '1200';
      host.style.width = 'min(360px, calc(100vw - 24px))';
      host.style.display = 'flex';
      host.style.flexDirection = 'column';
      host.style.gap = '10px';
      document.body.appendChild(host);
    }
    return host;
  }

  function dismissAlert(alertNode){
    if(!alertNode) return;
    alertNode.classList.add('is-leaving');
    setTimeout(()=>{
      if(alertNode.parentNode){
        alertNode.remove();
      }
    }, 260);
  }

  function alert(type,msg,position='body')
  {
    try{
      // Backward compatibility for calls like alert("some message")
      if(typeof msg === 'undefined'){
        msg = String(type !== undefined && type !== null ? type : '');
        type = 'error';
      }

      const safeType = type === 'success' ? 'success' : 'error';
      const icon = safeType === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-octagon-fill';
      const host = getAlertHost(position);
      if(!host) return nativeAlert(msg);

      const alertNode = document.createElement('div');
      alertNode.className = `custom-alert-toast custom-alert-${safeType}`;
      alertNode.setAttribute('role', 'alert');
      alertNode.style.background = 'rgba(255,255,255,0.98)';
      alertNode.style.border = '1px solid rgba(148,163,184,.25)';
      alertNode.style.borderRadius = '12px';
      alertNode.style.boxShadow = '0 12px 28px rgba(15,23,42,.14)';
      alertNode.style.overflow = 'hidden';
      alertNode.innerHTML = `
        <div class="custom-alert-content">
          <i class="bi ${icon} custom-alert-icon" aria-hidden="true"></i>
          <div class="custom-alert-message">${msg}</div>
          <button type="button" class="btn-close custom-alert-close" aria-label="Close"></button>
        </div>
        <div class="custom-alert-progress"></div>
      `;

      host.appendChild(alertNode);
      setTimeout(()=> alertNode.classList.add('is-visible'), 10);

      const closeBtn = alertNode.querySelector('.custom-alert-close');
      if(closeBtn){
        closeBtn.addEventListener('click', ()=> dismissAlert(alertNode));
      }

      setTimeout(()=> dismissAlert(alertNode), 3600);
    }
    catch(err){
      nativeAlert(typeof msg !== 'undefined' ? String(msg) : 'Something went wrong.');
    }
  }

  function remAlert(){
    const alertNode = document.querySelector('.custom-alert-toast');
    dismissAlert(alertNode);
  }

    
  function setActive()
  {
    let navbar = document.getElementById('dashboard-menu');
    let a_tags = navbar.getElementsByTagName('a');

    for(i=0; i<a_tags.length; i++)
    {
      let file = a_tags[i].href.split('/').pop();
      let file_name = file.split('.')[0];

      if(document.location.href.indexOf(file_name) >= 0){
        a_tags[i].classList.add('active');
      }

    }
  }
  setActive();
</script>