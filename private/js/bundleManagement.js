const addBundleItem = (e) => {
  e.preventDefault();
  const container = document.getElementById('createBundle-content');
  const els = document.querySelectorAll('[id^=bundle-item-container]');
  const lastSelect = els[els.length - 1];
  const newSelect = lastSelect.cloneNode(true);
  const splitName = newSelect.id.split('-');
  let newId = parseInt(splitName.pop()) + 1;
  newSelect.id = splitName.join('-') + '-' + newId;
  const newLabel = newSelect.querySelector('label');
  newLabel.innerText = 'bundle item ' + newId;
  newLabel.setAttribute('for', 'bundle-item-' + newId);
  const newSelector = newSelect.querySelector('select');
  newSelector.id = 'bundle-item-' + newId;
  newSelector.dataset.bundle_page_id = newId;
  const newTarget = newSelect.querySelector('[class^=bundle-item-options]');
  newTarget.id = 'bundle-item-options-' + newId;
  lastSelect.insertAdjacentElement('afterend', newSelect);
  container.style.maxHeight = `${container.scrollHeight}px`;
};

const bundleItemOptions = async (e) => {
  const container = document.getElementById('createBundle-content');

  let itemId = e.target.value;
  const pageBundleId = e.target.dataset.bundle_page_id;
  const url = '/private/functions/interface/stock/bundle_item_options.php?item_id=' + itemId + '&bundle_page_id=' + e.target.dataset.bundle_page_id;
  htmx
    .ajax('GET', url, {
      target: '#bundle-item-options-' + pageBundleId,
      swap: 'outerHTML',
    })
    .then(() => {
      container.style.maxHeight = `${container.scrollHeight}px`;
    });
};
