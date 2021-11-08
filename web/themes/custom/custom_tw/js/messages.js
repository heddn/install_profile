/**
 * @file
 * Message template overrides.
 */

((Drupal) => {

  /**
   * Overrides message theme function.
   *
   * @param {object} message
   *   The message object.
   * @param {string} message.text
   *   The message text.
   * @param {object} options
   *   The message context.
   * @param {string} options.type
   *   The message type.
   * @param {string} options.id
   *   ID of the message, for reference.
   *
   * @return {HTMLElement}
   *   A DOM Node.
   */
  Drupal.theme.message = ({ text }, { type, id }) => {
    const messagesTypes = Drupal.Message.getMessageTypeLabels();
    const messageWrapper = document.createElement('div');

    const classes = [
      'font-sans',
      'messages',
      'messages--' + type,
      'bg-no-repeat',
      'min-h-5',
      'my-2',
      'p-4',
      'border-solid',
      'border-l-4',
      type === 'status' ? 'bg-gray-400 border-black' : null,
      type === 'warning' ? 'text-black bg-yellow-100 border-yellow-300' : null,
      type === 'error' ? 'text-red-800 bg-pink-200 border-red-700' : null,
    ]

    messageWrapper.setAttribute('class', classes.join(' '));
    messageWrapper.setAttribute(
      'role',
      type === 'error' || type === 'warning' ? 'alert' : 'status',
    );
    messageWrapper.setAttribute('aria-labelledby', `${id}-title`);
    messageWrapper.setAttribute('data-drupal-message-id', id);
    messageWrapper.setAttribute('data-drupal-message-type', type);

    messageWrapper.innerHTML = `
    <div class="bg-no-repeat">
      <h2 id="${id}-title" class="visually-hidden">${messagesTypes[type]}</h2>
      <span class="messages__item font-bold pl-10">${text}</span>
    </div>
  `;

    return messageWrapper;
  };
})(Drupal);
