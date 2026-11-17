const updateItemPrice = (e) => {
  const url = '/functions/interface/shop/get_item_price.php?item_id=' + e.target.dataset.item_id + '&option_id=' + e.target.value;
  htmx.ajax('GET', url, {
    target: '#' + e.target.nextElementSibling.firstChild.id,
    swap: 'innerHTML',
    selectOOB: '#item-option-description',
  });
  // console.log(document.getElementById('itemPrice'));
  // const itemPriceEl = document.getElementById('itemPrice');
  // if (itemPriceEl) {
  //   const price = parseFloat(e.target.options[e.target.selectedIndex].dataset.price);
  //   itemPriceEl.innerHTML = price.toFixed(2);
  // }
};
