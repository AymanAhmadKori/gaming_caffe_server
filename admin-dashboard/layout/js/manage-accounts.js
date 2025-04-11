//=== Get Elements ===\\

// Containers
const pageContainer = document.querySelector('.pageContainer');
const accounts_container = document.querySelector('.accounts-container');

// Search elements
//Input
const search_input = document.getElementById('searchInput');

//Buttons
const by_name_btn = document.getElementById('search_by_name');
const by_id_btn = document.getElementById('search_by_id');
const by_email_btn = document.getElementById('search_by_email');

/* Nots
  - Save user data in indexedDB
  - Create refresh btn to user data
*/


// Create account element
function createAccountElement({id, full_name, email, entry_date, plans}) {

  const element = document.createElement('div');
  element.classList.add('account');

  // Set account elements
  element.innerHTML = `
    <div class="flex gap-10 fill-width hiddenOver">
      <div class="profile-img">
        <img src="${profile_img}" alt="${trans('صورة الملف الشخصي')}">
      </div>
      <div class="flex column grow-1 fill-width hiddenOver">
        <div class="flex align-center gap-5">
          <p id="name" title="${name}" class="txt body1 txt-color-gray"></p>
          <div class="id-container">
            <p id="id" class="txt body1"></p>
          </div>
        </div>
        <p id="email" title="${email}" class="txt body1 txt-color-gray"></p>
      </div>
    </div>

    <button id="detailsBtn" class="medium bgc">
      <p class="txt body1"><trans->التفاصيل</trans-></p>
    </button>
  `;

  // Get elements
  const name_element = element.querySelector('#name');
  const email_element = element.querySelector('#email');
  const id_element = element.querySelector('#id');
  const detailsBtn = element.querySelector('#detailsBtn');

  // Details window
  async function details() {
    // Get account subscription
    let sub_data = (await getAccountSub(id));
    let subs_history = (await getSubsHistory(id));

    // Validate sub_data
    (()=>{
      if(sub_data.error) {
        pushRealTimeAlert('error', sub_data.error);
        close();
        return;
      } 
      sub_data = sub_data === false? false : sub_data.data;

      if(sub_data) {
        let total_days = Math.round(
          (
            new Date(sub_data.expiry).getTime()
            - new Date(sub_data.entry_date).getTime()
          )
          / (24 * 3600 * 1000)
        );

        let days_left = Math.round(
          (
            new Date(sub_data.expiry).getTime()
            - new Date(getCurrentUTC()).getTime()
          ) / (24 * 3600 * 1000)
        );

        sub_data.total_days = total_days;
        sub_data.days_left = days_left;
        
        // Set plan data
        sub_data.plan = {
          name: plans[sub_data.plan_id].name,
          icon_name: plans[sub_data.plan_id].icon_name
        };
      }
    })();

    let detailsWindow = document.createElement('div');
    let overlayElement = document.createElement('div');

    // Set classes
    overlayElement.classList.add('overlayElement', 'flex', 'align-center', 'just-center');
    detailsWindow.classList.add('detailsWindow');

    // Window header elements
    detailsWindow.innerHTML = `
      <header>
        <div id="label" class="flex align-center just-between">
          <h5>${trans('تفاصيل الحساب')}</h5>

          <button id="close" title="${trans('اغلاق')}" class="medium bgc">
            <i- type="remove-x" class="small"></i->
          </button>
        </div>

        <div id="profile-data" class="flex column align-center gap-10">
          <div class="profile-img">
            <img src="${profile_img}" alt="${trans('صورة الملف الشخصي')}" />
          </div>
          <div class="flex column align-center gap-5">
            <h6 id="name"></h6>
            <p id="email" class="txt body1"></p>
          </div>
        </div>
        
        <div class="sectionsBar flex gap-10" style="padding: 10px 15px 0">
          <button id="subs_section_handel" class="medium bgc-primary">
            <p class="txt body1">${trans('الأشتراك')}</p>
          </button>
          <button ${subs_history.length == 0? "disabled" : ""} id="history_section_handel" class="medium bgc">
            <p class="txt body1">${trans('السجل')}</p>
            </button>
        </div>
        
      </header>
    `;

    // Subscription section
    detailsWindow.innerHTML += `
      <section class="subscription">
        ${sub_data === false? '': `
          <div class="sub ${sub_data.plan.name}">
            <div id="planDetails" class="flex align-center gap-10">
              <i- class="small" type="${sub_data.plan.icon_name}"></i->
              <p class="txt body1 flex grow-1 fill-width">${trans(sub_data.plan.name)}</p>
              
              <button id="cancelSubBtn" class="small">
                <p class="txt body1 txt-color-red">${trans('إلغاء')}</p>
              </button>

              
            </div>

            <hr/>

            <div class="flex column gap-10">
            
              <div class="flex gap-10 align-center">
                <p class="txt body1">${trans('ايام متبقية')}</p>
                <div class="highlight txt body1">
                  <span>${sub_data.days_left}</span>
                  <span>/</span>
                  <span>${sub_data.total_days}</span>
                </div>
              </div>
              
              <div class="flex gap-10 align-center">
                <p class="txt body1">${trans('التكلفة')}</p>
                <div class="highlight txt body1 flex gap-3">
                  <p class="txt body1">${sub_data.cost}</p>
                  <p class="txt body1">${trans('د.ل')}</p>
                </div>
              </div>

              <div class="flex gap-10 align-center">
                <p class="txt body1">${trans('انتهاء الصلاحية')}</p>
                <div class="highlight txt body1">${formatReadableDate(sub_data.expiry)}</div>
              </div>

              <p class="txt body1 txt-color-gray" style="padding-top: 10px">${formatReadableDate(sub_data.entry_date)}</p>

            </div>
            
          </div>
        `}
        <div class="noSub display-none">
          <h5>${trans('لا يوجد اشتراك')}</h5>
          <button id="addNewSubBtn" class="medium bgc">
            <div class="txt body1">${trans('اشتراك جديد')}</div>
          </button>
        </div>
      </section>
    `;
    
    // History section
    detailsWindow.innerHTML += `
    <section class="history flex column gap-10 hidden">
      <div class="highlight bgc-primary">
        <p class="txt body1">${subs_history.length}</p>
      </div>
      <div id="subs_history_container" class="flex column gap-10"></div>
    </section>
    `;

    // Add new subscription
    let addNewSubBtn = detailsWindow.querySelector('#addNewSubBtn');

    function newSubscription() {
      let set_plan_section = document.createElement('section');
      set_plan_section.classList.add('page', 'plan_section');

      // Sub data
      let newSubData = {
        plan_id : null,
        cost : null,
        account_id : id,
        cycles : 1
      }

      // Select Plan
      set_plan_section.innerHTML = `
        <h6>${trans('الخُطط')}</h6>
        <div id="select_plans" class="flex gap-10"></div>
      `;

      // Set cycle
      set_plan_section.innerHTML += `
        <div class="flex align-center gap-10">
          <h6>${trans('الدورات')}</h6>
          <div class="highlight bgc flex gap-5 align-center">
            <p id="price_per_cycle" class="txt body1">95.00</p>
            <p class="txt body2">${trans('د.ل')}</p>
          </div>
        </div>

        <div class="flex align-center gap-10">
          <input-compo>
            <div class="input">
              <input id="cycles_input" type="number" min="1"/>
            </div>
          </input-compo>
          <div class="highlight bgc flex gap-5 align-center">
            <p id="cycle_duration" class="txt body1"></p>
            <p class="txt body1">${trans('ايام')}</p>
          </div>
        </div>       
      `;

      // Action buttons
      set_plan_section.innerHTML += `
        <div class="flex fill-height align-end just-end gap-10">
          <button id="cancelBtn" class="medium bgc">
            <p class="txt body1">${trans('إلغاء')}</p>
          </button>
          <button id="registerBtn" class="medium bgc-primary">
            <p class="txt body1">${trans('تسجيل')}</p>
          </button>
        </div>
      `;

      // Close section
      let cancelBtn = set_plan_section.querySelector('#cancelBtn');
      cancelBtn.addEventListener('click', ()=> set_plan_section.remove());
      
      // Get elements
      let select_plan_container = set_plan_section.querySelector('#select_plans');
      let cycle_price = set_plan_section.querySelector('#price_per_cycle');
      let cycle_input = set_plan_section.querySelector('#cycles_input');
      let cycle_duration = set_plan_section.querySelector('#cycle_duration');
      let registerBtn = set_plan_section.querySelector('#registerBtn');
      
      // Show plans
      let plansBtns = [];
      for(let plan in plans) { plan = plans[plan];
        let select_btn = document.createElement('button');
        select_btn.classList.add('medium', 'bgc');

        select_btn.innerHTML = `
          <i- type="${plan.icon_name}" class="small"></i->
          <p class="txt body1">${trans(plan.name)}</p>
        `;
        
        select_btn.addEventListener('click', ()=>{
          // Set new sub data
          newSubData.plan_id = plan.id;
          newSubData.cost = parseFloat(plan.price_per_cycle);
          cycle_duration.innerText = plan.cycle_duration;
          cycle_input.value = 1;
          cycle_price.innerText = newSubData.cost;
          
          
          // Remove primary color from all btns
          plansBtns.forEach(btn => {
            btn.classList.remove('bgc-primary');
            btn.classList.add('bgc');
          })

          // Add primary color
          select_btn.classList.add('bgc-primary');
          select_btn.classList.remove('bgc');
        })
        
        plansBtns.push(select_btn);
        select_plan_container.append(select_btn);

        if(newSubData.plan_id === null) select_btn.click();
      }
      
      // Cycle input
      cycle_input.addEventListener('input', ()=>{
        let value = parseInt(cycle_input.value.trim());
        let min = cycle_input.getAttribute('min') ?? 1;

        if(isNaN(value) || (value <= 0 && typeof value !== 'number')) value = min;
        
        // Update cycles
        newSubData.cycles = value;

        // Update Cost
        newSubData.cost = parseFloat(plans[newSubData.plan_id].price_per_cycle) * value;
        
        cycle_duration.innerText = plans[newSubData.plan_id].cycle_duration * value;
      })
      
      // Register Btn
      registerBtn.addEventListener('click', async ()=>{

        // Disabled registerBtn
        registerBtn.setAttribute('disabled', '');
        
        let plan = plans[newSubData.plan_id];
        
        let expiry = new Date(
          new Date(getCurrentUTC()).getTime()
          + ((newSubData.cycles * plan.cycle_duration) * 24 * 3600 * 1000)
        ) .toISOString();

        
        function check() {
          return new Promise((resolve) => {
            let checkSection = document.createElement('section');
            checkSection.classList.add('overlay', 'flex', 'column', 'gap-20', 'align-center');
            checkSection.style.paddingTop = '10px';
    
            checkSection.innerHTML = `
              <h6>${trans('هل تريد تسجيل الاشتراك ؟')}</h6>
              <div style="max-width: 160px;" class="flex align-center just-between fill-width">
                <p class="txt body1">${trans('التكلفة')}</p>
                <div class="highlight bgc flex gap-3">
                  <h6 class="txt-color-green">${newSubData.cost}</h6>
                  <h6 class="txt-color-green">${trans('د.ل')}</h6>
                </div>
              </div>
              
              <div class="flex align-center gap-10">
                <button id="rejectBtn" class="medium bgc">
                  <p class="txt body1">${trans('إلغاء')}</p>
                </button>
                <button id="resolveBtn" class="medium bgc-green">
                  <p class="txt body1">${trans('نعم، تسجيل الاشتارك')}</p>
                </button>
              </div>
            `;
  
            let rejectBtn = checkSection.querySelector('#rejectBtn');
            let resolveBtn = checkSection.querySelector('#resolveBtn');
  
            resolveBtn.addEventListener('click', ()=>{resolve(true); close()});
            rejectBtn.addEventListener('click', ()=>{
              resolve(false);
              checkSection.remove();
            });
  
            detailsWindow.append(checkSection);
          })
        }
        
        if( !(await check()) ) {
          registerBtn.removeAttribute('disabled')
          return;
        }
        
        let request = await set_sub(id, plan.id, newSubData.cost, expiry);
        
        if(request.error) {
          pushRealTimeAlert('error', request.error);
          close();
          return;
        }
        
        
        if(request.data === false) {
          pushRealTimeAlert('error', trans('حدث خطأ. حاول مرة أخرى.'))
          close();
          return;
        }
        if(request.data === true) {
          pushRealTimeAlert('success', trans('تم تسجيل الاشتارك'))
          close();
          return;
        }
        
        // Enable registerBtn
        registerBtn.removeAttribute('disabled');
      })
      
      // Show section
      detailsWindow.append(set_plan_section);
    }

    if(sub_data === false) {
      addNewSubBtn.addEventListener('click', newSubscription);
    }
    
    // Subscription history
    let subs_history_container = detailsWindow.querySelector('#subs_history_container');    
    subs_history.forEach(sub => {
      sub.plan = plans[sub.plan_id];
      
      subs_history_container.innerHTML += `
        <div class="sub ${sub.plan.name}">
          <div id="planDetails" class="flex align-center space-between">
            <i- class="small" type="${sub.plan.icon_name}"></i->
            <p class="txt body1">${trans(sub.plan.name)}</p>
          </div>
          <div class="flex align-center gap-5">
            <p class="txt body1 txt-color-gray">${formatReadableDate(sub.entry_date)}</p>
            <button class="drop-down">
              <i- type="arrow-left" class="small"></i->
            </button>
          </div>
        </div>
      `
    })

    
    async function cancelSub() {

      function check() {
        return new Promise((resolve) => {
          let checkSection = document.createElement('section');
          checkSection.classList.add('overlay', 'flex', 'column', 'gap-20', 'align-center');
          checkSection.style.paddingTop = '10px';
  
          checkSection.innerHTML = `
            <h6 class="txt txt-color-red txt-center">${trans('هل انت متأكد بأنك تريد إلغاء الاشتراك ؟')}</h6>
            <div class="flex align-center gap-10">
              <button id="rejectBtn" class="medium bgc">
                <p class="txt body1">${trans('إلغاء')}</p>
              </button>
              <button id="resolveBtn" class="medium bgc-red">
                <p class="txt body1">${trans('نعم، إلغاء الاشتارك')}</p>
              </button>
            </div>
          `;

          let rejectBtn = checkSection.querySelector('#rejectBtn');
          let resolveBtn = checkSection.querySelector('#resolveBtn');

          resolveBtn.addEventListener('click', ()=>{resolve(true); close()});
          rejectBtn.addEventListener('click', ()=>{
            resolve(false);
            checkSection.remove();
          });

          detailsWindow.append(checkSection);
        })
      }

      if(!(await check())) return;
      
      let data = await cancel_sub(id);
      
      if(data === false) {
        pushRealTimeAlert('error', trans('حدث خطأ. حاول مرة أخرى.'));
        close();
        return;
      }
      if(data === true) {
        pushRealTimeAlert('success', trans('تم إلغاء الاشتارك'));
        close();
      }
    }

    // cancel sub
    let cancelBtn = detailsWindow.querySelector('#cancelSubBtn');
    if(cancelBtn) cancelBtn.addEventListener('click', cancelSub);
    
    // Show email & name
    detailsWindow.querySelector('#name').innerText = full_name;
    detailsWindow.querySelector('#email').innerText = email;
    
    // Get elements
    let closeBtn = detailsWindow.querySelector('#close');

    // Close window
    function close() {
      overlayElement.remove();
    }
    closeBtn.addEventListener('click', close);
    overlayElement.addEventListener('click', event =>{
      if(event.target == overlayElement) close();
    })
    
    // Sections handels
    let subs_section_handel = detailsWindow.querySelector('#subs_section_handel');
    let history_section_handel = detailsWindow.querySelector('#history_section_handel');

    // Sections
    let subscription_section = detailsWindow.querySelector('section.subscription');
    let history_section = detailsWindow.querySelector('section.history');

    function openSubsSection() {
      // Add primary color to subs handel
      subs_section_handel.classList.add('bgc-primary');
      subs_section_handel.classList.remove('bgc');

      // Remove primary color from ather handels
      history_section_handel.classList.add('bgc');
      history_section_handel.classList.remove('bgc-primary');
      
      // Show subscription section
      subscription_section.classList.remove('hidden');

      // Hide ather sections
      history_section.classList.add('hidden');
    }
    function openHistorySection() {
      // Add primary color to history handels
      history_section_handel.classList.add('bgc-primary');
      history_section_handel.classList.remove('bgc');

      // Remove primary color from subs handel
      subs_section_handel.classList.add('bgc');
      subs_section_handel.classList.remove('bgc-primary');
      
      // Show subscription section
      history_section.classList.remove('hidden');

      // Hide ather sections
      subscription_section.classList.add('hidden');
    }

    subs_section_handel.addEventListener('click', openSubsSection);
    history_section_handel.addEventListener('click', openHistorySection);


    
    // Diplay
    overlayElement.append(detailsWindow);
    document.body.append(overlayElement);
  }
  
  // Show user data
  name_element.innerHTML = full_name;
  id_element.innerHTML = id;
  email_element.innerHTML = email;

  // Active details button
  detailsBtn.addEventListener('click', details);
  
  // Append account
  accounts_container.append(element);
}

// API functions
async function ServerFetch(body, responseMode = "json") {
  if( !(['json', 'text'].includes(responseMode))) return null;
  // const API_URI = "http://192.168.221.152/projects/github/gaming_caffe_server/APIs/manag-accounts.php";
  const API_URI = "http://localhost/projects/github/gaming_caffe_server/APIs/manag-accounts.php";
  
  const request = await fetch(API_URI, {
    method: 'POST',
    body
  })
  
  return await request[responseMode]();
}

async function getAccounts(limit, except_IDs) {
  // Body data
  const body = JSON.stringify({
    mode: 'getAll-accounts',
    limit: limit,
    except: except_IDs
  });
  
  let request = await ServerFetch(body);

  return request;
}

async function getAccountSub(account_id) {
  const body = JSON.stringify({
    mode: 'get-sub',
    account_id
  })

  const request = await ServerFetch(body);

  return request;  
}

async function getsubPlans() {
  const body = JSON.stringify({
    mode: 'getAll-plans'
  });

  return await ServerFetch(body);
}

async function getSubsHistory(account_id, except_subs_IDs = []) {
  const body = JSON.stringify({
    mode: 'getAll-subs-history',
    limit: 6,
    except: except_subs_IDs,
    of_account: account_id
  });

  const request = await ServerFetch(body);

  if(request.error) {
    pushRealTimeAlert('error', request.error);
    return;
  }

  
  return request.data;
}

async function set_sub(account_id, plan_id, cost, expiry) {
  const body = JSON.stringify({
    mode: 'set-sub',
    account_id, 
    plan_id,
    cost,
    expiry
  });

  const request = ServerFetch(body);

  if(request.error) {
    pushRealTimeAlert('error', request.error);
    return false;
  }
  
  return request;
}
async function cancel_sub(account_id) {
  const body = JSON.stringify({
    mode: 'cancel-sub',
    account_id
  });

  const request = await ServerFetch(body);

  if(request.error) {
    pushRealTimeAlert('error', request.error);
    return false;
  }
  return request.data;
}

(async ()=>{
  // Get subs plans & accounts 
  let plans = await getsubPlans();
  const accounts = await getAccounts(9, []);
  
  // Validate accounts & plans
  if( accounts.error ) {
    pushRealTimeAlert('error', accounts.error);
    return;
  }
  if( plans.error ) {
    pushRealTimeAlert('error', plans.error);
    return;
  }

  // Plan id to kay
  plans = (()=>{
    let result = {};

    for(let plan of plans.data) {
      result[plan.id] = plan;
    }
    return result;
  })();
  
  // Show all accounts
  accounts.data.forEach(account => createAccountElement({...account, plans: plans}));
  
})();