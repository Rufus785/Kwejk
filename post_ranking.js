document.addEventListener("DOMContentLoaded", function () {
  const tabButtons = document.querySelectorAll(".tab-button");
  const tabContents = document.querySelectorAll(".tab-content");
  const filterButtons = document.querySelectorAll(".filter-button");
  const postsList = document.getElementById("posts-list");

  tabButtons.forEach((button) => {
      button.addEventListener("click", () => {
          const tabName = button.getAttribute("data-tab");
          tabButtons.forEach((btn) => btn.classList.remove("active"));
          tabContents.forEach((content) => content.classList.remove("active"));
          button.classList.add("active");
          document.getElementById(`${tabName}-ranking`).classList.add("active");
      });
  });

  filterButtons.forEach((button) => {
      button.addEventListener("click", () => {
          const filter = button.getAttribute("data-filter");

          fetch(`fetch_posts.php?filter=${filter}`)
              .then((response) => response.json())
              .then((data) => {
                  postsList.innerHTML = "";
                  data.forEach((post) => {
                      const listItem = document.createElement("li");
                      listItem.innerHTML = `
                          <div class="ranking-info">
                              <span class="title">${post.title}</span>
                              <span class="points">${post.points} likes</span>
                          </div>
                          <img src="${post.image}" alt="${post.title}" />
                      `;
                      postsList.appendChild(listItem);
                  });
              })
              .catch((error) => {
                  console.error("Error:", error);
              });
      });
  });
});
