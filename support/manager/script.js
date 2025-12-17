 // Получаем все элементы списка обращений
 const ticketItems = document.querySelectorAll('.ticket-item');

 // Получаем модальное окно и элементы внутри него
 const modal = document.getElementById('ticketModal');
 const modalTicketId = document.getElementById('modalTicketId');
 const modalTicketTitle = document.getElementById('modalTicketTitle');
 const modalTicketStatus = document.getElementById('modalTicketStatus');
 const closeButton = document.querySelector('.close-button');

 // Добавляем обработчик клика для каждого элемента списка
 ticketItems.forEach(item => {
     item.addEventListener('click', () => {
         // Получаем данные из атрибутов data-
         const ticketId = item.dataset.ticketId;
         const ticketTitle = item.dataset.ticketTitle;
         const ticketStatus = item.dataset.ticketStatus;

         // Заполняем модальное окно данными
         modalTicketId.textContent = ticketId;
         modalTicketTitle.textContent = ticketTitle;
         modalTicketStatus.textContent = ticketStatus;
         modalTicketStatus.className = 'ticket-status ' + ticketStatus; // Обновляем класс для статуса

         // Открываем модальное окно
         modal.style.display = 'block';
     });
 });

 // Закрываем модальное окно при клике на крестик
 closeButton.addEventListener('click', () => {
     modal.style.display = 'none';
 });

 // Закрываем модальное окно при клике вне его
 window.addEventListener('click', (event) => {
     if (event.target === modal) {
         modal.style.display = 'none';
     }
 });