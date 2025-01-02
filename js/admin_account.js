const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    const toggleBtn = document.getElementById('toggleBtn');
    const toggleIcon = toggleBtn.querySelector('i');
    const inventoryDropdown = document.getElementById('inventoryDropdown');
    const dropdownItems = document.getElementById('dropdownItems');
    const dropdownIcon = inventoryDropdown.querySelector('.dropdown-icon');
    const userProfile = document.querySelector('.user-profile');
    const accountsItem = document.getElementById('accountsItem');
    const viewInventory = document.getElementById('viewInventory');
    const dashboardItem = document.getElementById('dashboardItem');

    // Sidebar toggle
    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        content.classList.toggle('collapsed');
        toggleBtn.classList.toggle('collapsed');

        // Change icon based on state
        toggleIcon.classList.toggle('fa-chevron-right', sidebar.classList.contains('collapsed'));
        toggleIcon.classList.toggle('fa-chevron-left', !sidebar.classList.contains('collapsed'));
    });

    // Inventory dropdown toggle
    inventoryDropdown.addEventListener('click', () => {
        dropdownItems.classList.toggle('dropdown-open');
        dropdownIcon.classList.toggle('fa-chevron-up');
        dropdownIcon.classList.toggle('fa-chevron-down');
        // Adjust "Accounts" distance
        if (dropdownItems.classList.contains('dropdown-open')) {
        accountsItem.style.marginTop = '0'; // Remove extra space when dropdown is open
    } else {
        accountsItem.style.marginTop = '10px'; // Add some space when dropdown is closed
    }

        userProfile.style.bottom = dropdownItems.classList.contains('dropdown-open') ? '-150px' : '20px';
    });

    accountsItem.addEventListener('click', () => {
        document.querySelectorAll('.sidebar-item').forEach(item => item.classList.remove('active'));
        accountsItem.classList.add('active');
    });

    document.addEventListener('DOMContentLoaded', () => {
    const inventoryDropdown = document.getElementById('inventoryDropdown');
    const dropdownItems = document.getElementById('dropdownItems');
    const dropdownIcon = inventoryDropdown.querySelector('.dropdown-icon');

    // Check if any child item is active on page load
    const isChildActive = Array.from(dropdownItems.querySelectorAll('.sidebar-item')).some(item =>
        item.classList.contains('active')
    );

    if (isChildActive) {
        dropdownItems.classList.add('dropdown-open');
        dropdownIcon.classList.remove('fa-chevron-down');
        dropdownIcon.classList.add('fa-chevron-up');
    }

    // Sidebar dropdown toggle
    inventoryDropdown.addEventListener('click', () => {
        if (!isChildActive) {
            dropdownItems.classList.toggle('dropdown-open');
            dropdownIcon.classList.toggle('fa-chevron-up');
            dropdownIcon.classList.toggle('fa-chevron-down');
        }
    });
});


    function openProfile() {
    // You can redirect to a profile page or open a modal, etc.
    window.location.href = 'profile_page.html'; // Example redirection to profile page
    }

    document.getElementById("addUserForm").addEventListener("submit", function (e) {
        e.preventDefault(); // Prevent form from submitting the traditional way
    
        const formData = new FormData(this);
    
        fetch("add_user.php", {
            method: "POST",
            body: formData,
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Add new user to the table dynamically
                    const tableBody = document.querySelector("#useraccount_table tbody");
                    const newRow = document.createElement("tr");
    
                    newRow.setAttribute("data-user-id", data.UserID); // Use ID from server response
                    newRow.innerHTML = `
                        <td>${formData.get("firstname")} ${formData.get("lastname")}</td>
                        <td>${formData.get("email")}</td>
                        <td>${formData.get("role")}</td>
                        <td>${formData.get("birthdate")}</td>
                        <td>${formData.get("contact_number")}</td>
                        <td>Active</td>
                        <td class='text-center'>
                            <button class='btn btn-success btn-sm' title='Edit' style='margin-right: 8px;'>
                                <i class='fas fa-edit'></i>
                            </button>
                            <button class='btn btn-danger btn-sm delete-btn' data-bs-toggle='modal' data-bs-target='#deleteModal' title='Delete' data-user-id='${data.UserID}'>
                                <i class='fas fa-trash'></i>
                            </button>
                        </td>
                    `;
                    tableBody.appendChild(newRow);
    
                    // Close the modal
                    const addUserModal = bootstrap.Modal.getInstance(document.getElementById("addUserModal"));
                    addUserModal.hide();
    
                    // Reattach delete button handlers
                    attachDeleteHandlers();
    
                    alert("User added successfully!");
                } else {
                    alert(data.message || "Error adding user.");
                }
            })
            .catch(error => console.error("Error:", error));
    });

    document.getElementById("email").addEventListener("blur", function() {
        const email = document.getElementById("email").value;
        const feedback = document.getElementById("emailFeedback");
        
        if (email) {
            // Send an AJAX request to check if email exists
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "check_email.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.status === "error") {
                        feedback.textContent = response.message;
                        feedback.style.display = "block";
                    } else {
                        feedback.style.display = "none";
                    }
                }
            };
            xhr.send("email=" + encodeURIComponent(email));
        }
    });

    function checkAge() {
        const birthdate = document.getElementById("birthdate").value;
        const feedback = document.getElementById("birthdateFeedback");
    
        if (!birthdate) return; // Skip if the field is empty
    
        const currentDate = new Date();
        const birthDate = new Date(birthdate);
        const age = currentDate.getFullYear() - birthDate.getFullYear();
        const monthDifference = currentDate.getMonth() - birthDate.getMonth();
        const isUnderage = age < 15 || (age === 15 && monthDifference < 0);
    
        if (isUnderage) {
            feedback.textContent = "You must be at least 15 years old to register.";
            feedback.style.display = "block";
        } else {
            feedback.textContent = "";
            feedback.style.display = "none";
        }
    }

    const today = new Date().toISOString().split('T')[0];

  // Set the max attribute to today's date
  document.getElementById('birthdate').setAttribute('max', today);
  document.getElementById('editBirth').setAttribute('max', today);


  let userIdToDelete = null;

  // Handle delete button click
  document.querySelectorAll('.btn-danger').forEach(button => {
      button.addEventListener('click', function () {
          const userRow = this.closest("tr");
          userIdToDelete = this.getAttribute('data-user-id'); // Store the user ID from the button's data-user-id attribute
          const deleteModal = new bootstrap.Modal(document.getElementById("deleteModal"));
          deleteModal.show();
      });
  });
  
  // Handle delete confirmation
  document.getElementById("confirmDeleteBtn").addEventListener("click", function () {
      if (!userIdToDelete) return;
  
      fetch("delete_user.php", {
          method: "POST",
          headers: {
              "Content-Type": "application/x-www-form-urlencoded"
          },
          body: new URLSearchParams({ UserID: userIdToDelete })
      })
          .then(response => response.json())
          .then(data => {
              if (data.success) {
                  // Remove the deleted row from the table
                  const row = document.querySelector(`tr[data-user-id="${userIdToDelete}"]`);
                  if (row) row.remove();
  
                  const deleteModal = bootstrap.Modal.getInstance(document.getElementById("deleteModal"));
                  deleteModal.hide();
              } else {
                  alert(data.error || "Failed to delete the user.");
              }
          })
          .catch(error => console.error("Error:", error));
  });
  
  