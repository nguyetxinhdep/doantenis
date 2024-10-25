<div class="modal fade" id="fixedScheduleModal" tabindex="-1" aria-labelledby="fixedScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fixedScheduleModalLabel">Đặt lịch cố định</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="fixedScheduleForm">
                    <div class="row mb-3">
                        <div class="col">
                            <label for="startDate" class="form-label">Từ ngày:</label>
                            <input type="date" id="startDate" class="form-control" required>
                        </div>
                        <div class="col">
                            <label for="endDate" class="form-label">Đến ngày:</label>
                            <input type="date" id="endDate" class="form-control" required>
                        </div>
                    </div>
                    <div id="scheduleContainer">
                        <div class="schedule-group mb-3"
                            style="padding: 15px; border: 1px solid #ccc; border-radius: 5px;">
                            <div class="row mb-3">
                                <div class="col">
                                    <label for="days" class="form-label small">Chọn thứ</label>
                                    <select class="form-select days">
                                        <option value="1">Thứ Hai</option>
                                        <option value="2">Thứ Ba</option>
                                        <option value="3">Thứ Tư</option>
                                        <option value="4">Thứ Năm</option>
                                        <option value="5">Thứ Sáu</option>
                                        <option value="6">Thứ Bảy</option>
                                        <option value="0">Chủ Nhật</option>
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-danger btn-sm removeSchedule"
                                        style="display: none;">-</button>
                                </div>
                            </div>
                            <div class="scheduleDetails" style="">
                                <div class="courtsContainer mb-3">
                                    <label for="courts" class="form-label small">Chọn sân</label>
                                    <div class="court-group">
                                        <div class="input-group mb-2 px-4">
                                            <select class="form-select courts">
                                                <option value="court1">Sân 1</option>
                                                <option value="court2">Sân 2</option>
                                                <option value="court3">Sân 3</option>
                                            </select>
                                            <button type="button" class="btn btn-secondary btn-sm addCourt">+</button>
                                        </div>
                                        <div class="timesContainer">
                                            <div class="timeGroup mb-2 px-5">
                                                <label class="form-label">Nhập giờ cho court3</label>
                                                <div class="input-group">
                                                    <input type="time" class="form-control start-time"
                                                        required="">
                                                    <span class="input-group-text">đến</span>
                                                    <input type="time" class="form-control end-time" required="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary" id="addSchedule">+</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="submitFixedSchedule">Đặt lịch</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('addSchedule').addEventListener('click', function() {
        const scheduleContainer = document.getElementById('scheduleContainer');

        // Tạo một nhóm lịch mới
        const newScheduleGroup = document.createElement('div');
        newScheduleGroup.classList.add('schedule-group', 'mb-3');
        newScheduleGroup.style.padding = '15px';
        newScheduleGroup.style.border = '1px solid #ccc';
        newScheduleGroup.style.borderRadius = '5px';

        newScheduleGroup.innerHTML = `
        <div class="row mb-3">
            <div class="col">
                <label for="days" class="form-label">Chọn thứ</label>
                <select class="form-select days">
                    <option value="1">Thứ Hai</option>
                    <option value="2">Thứ Ba</option>
                    <option value="3">Thứ Tư</option>
                    <option value="4">Thứ Năm</option>
                    <option value="5">Thứ Sáu</option>
                    <option value="6">Thứ Bảy</option>
                    <option value="0">Chủ Nhật</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger btn-sm removeSchedule">-</button>
            </div>
        </div>
        <div class="scheduleDetails" style="display: none;">
            <div class="courtsContainer mb-3">
                <label for="courts" class="form-label">Chọn sân</label>
                <div class="court-group">
                    <div class="input-group mb-2 px-4">
                        <select class="form-select courts">
                            <option value="court1">Sân 1</option>
                            <option value="court2">Sân 2</option>
                            <option value="court3">Sân 3</option>
                        </select>
                        <button type="button" class="btn btn-secondary btn-sm addCourt">+</button>
                    </div>
                    <div class="timesContainer"></div>
                </div>
            </div>
        </div>
    `;

        scheduleContainer.appendChild(newScheduleGroup);

        // Lắng nghe sự kiện cho các trường mới
        addListenersToNewFields(newScheduleGroup);
    });

    function addListenersToNewFields(scheduleGroup) {
        const daysSelect = scheduleGroup.querySelector('.days');
        const courtsContainer = scheduleGroup.querySelector('.courtsContainer');
        const courtsSelect = scheduleGroup.querySelector('.courts');
        const timesContainer = scheduleGroup.querySelector('.timesContainer');
        const removeButton = scheduleGroup.querySelector('.removeSchedule');
        const scheduleDetails = scheduleGroup.querySelector('.scheduleDetails');

        // Lắng nghe sự kiện thay đổi của trường chọn thứ
        daysSelect.addEventListener('change', function() {
            if (this.value) {
                scheduleDetails.style.display = 'block';
            } else {
                scheduleDetails.style.display = 'none';
                timesContainer.style.display = 'none'; // Ẩn khung giờ nếu không có ngày nào được chọn
            }
        });

        // Lắng nghe sự kiện nhấn nút xóa
        removeButton.addEventListener('click', function() {
            scheduleGroup.remove();
        });

        // Nút thêm sân
        scheduleGroup.querySelector('.addCourt').addEventListener('click', function() {
            const courtGroup = document.createElement('div');
            courtGroup.classList.add('court-group', 'mb-2');

            courtGroup.innerHTML = `
            <div class="input-group px-4">
                <select class="form-select courts">
                    <option value="court1">Sân 1</option>
                    <option value="court2">Sân 2</option>
                    <option value="court3">Sân 3</option>
                </select>
                <button type="button" class="btn btn-danger btn-sm removeCourt">-</button>
            </div>
            <div class="timesContainer mb-2"></div>
        `;

            courtsContainer.appendChild(courtGroup);

            // Lắng nghe sự kiện cho nút xóa sân
            courtGroup.querySelector('.removeCourt').addEventListener('click', function() {
                courtGroup.remove();
            });

            // Lắng nghe sự kiện thay đổi của trường chọn sân mới
            courtGroup.querySelector('.courts').addEventListener('change', function() {
                const selectedCourts = Array.from(this.selectedOptions).map(option => option.value);
                const timesContainer = courtGroup.querySelector('.timesContainer');

                // Xóa giờ cũ
                timesContainer.innerHTML = '';

                if (selectedCourts.length > 0) {
                    // Hiện giờ nếu có sân được chọn
                    const timeGroup = document.createElement('div');
                    timeGroup.classList.add('timeGroup', 'mb-2', 'px-5');

                    timeGroup.innerHTML = `
                    <label class="form-label">Nhập giờ cho ${selectedCourts.join(', ')}</label>
                    <div class="input-group">
                        <input type="time" class="form-control start-time" required>
                        <span class="input-group-text">đến</span>
                        <input type="time" class="form-control end-time" required>
                    </div>
                `;
                    timesContainer.appendChild(timeGroup);
                }
            });
        });

        // Hiển thị giờ khi nhóm lịch đầu tiên được tạo
        const initialCourtsSelect = courtsSelect;
        const initialTimesContainer = courtsContainer.querySelector('.timesContainer');

        initialCourtsSelect.addEventListener('change', function() {
            const selectedCourts = Array.from(this.selectedOptions).map(option => option.value);
            initialTimesContainer.innerHTML = '';

            if (selectedCourts.length > 0) {
                const timeGroup = document.createElement('div');
                timeGroup.classList.add('timeGroup', 'mb-2', 'px-5');

                timeGroup.innerHTML = `
                <label class="form-label">Nhập giờ cho ${selectedCourts.join(', ')}</label>
                <div class="input-group">
                    <input type="time" class="form-control start-time" required>
                    <span class="input-group-text">đến</span>
                    <input type="time" class="form-control end-time" required>
                </div>
            `;
                initialTimesContainer.appendChild(timeGroup);
            }
        });
    }

    // Bắt đầu lắng nghe sự kiện cho các trường đã có
    document.querySelectorAll('.schedule-group').forEach(scheduleGroup => {
        addListenersToNewFields(scheduleGroup);
    });

    document.getElementById('submitFixedSchedule').addEventListener('click', function() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const schedules = [];

        // Lặp qua tất cả các nhóm lịch
        document.querySelectorAll('.schedule-group').forEach(scheduleGroup => {
            const days = scheduleGroup.querySelector('.days').value;
            const courtsData = [];

            // Lặp qua tất cả các sân trong nhóm lịch
            scheduleGroup.querySelectorAll('.court-group').forEach(courtGroup => {
                const court = courtGroup.querySelector('.courts').value;
                const startTimeInput = courtGroup.querySelector('.start-time'); // Input giờ vào
                const endTimeInput = courtGroup.querySelector('.end-time'); // Input giờ ra

                // Lấy giá trị giờ vào và giờ ra
                const startTime = startTimeInput ? startTimeInput.value : '';
                const endTime = endTimeInput ? endTimeInput.value : '';

                courtsData.push({
                    court: court,
                    startTime: startTime,
                    endTime: endTime,
                });
            });

            schedules.push({
                day: days,
                courts: courtsData,
            });
        });

        // Gửi dữ liệu đi hoặc xử lý thêm theo yêu cầu
        const dataToSend = {
            startDate: startDate,
            endDate: endDate,
            schedules: schedules,
        };

        console.log(dataToSend); // Hiển thị dữ liệu ra console
    });
</script>
