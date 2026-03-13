package com.gtcrais.native.push_notifications_permission_watcher

import android.Manifest
import android.content.Context
import android.content.pm.PackageManager
import android.os.Build
import android.os.Bundle
import android.os.Handler
import android.os.Looper
import androidx.activity.result.contract.ActivityResultContracts
import androidx.core.app.NotificationManagerCompat
import androidx.core.content.ContextCompat
import androidx.fragment.app.Fragment
import androidx.fragment.app.FragmentActivity
import com.nativephp.mobile.bridge.BridgeFunction
import com.nativephp.mobile.bridge.BridgeResponse
import com.nativephp.mobile.utils.NativeActionCoordinator
import org.json.JSONObject

class PermissionFragment : Fragment() {
    private var hasLaunched = false

    private val launcher = registerForActivityResult(
        ActivityResultContracts.RequestPermission()
    ) { isGranted ->
        val activity = requireActivity()
        val eventClass = arguments?.getString("eventClass") ?: return@registerForActivityResult

        val actualStatus = PushNotificationsPermissionWatcherFunctions.resolvePermissionStatus(activity)
        if (actualStatus != "not_determined") {
            Handler(Looper.getMainLooper()).post {
                val payload = JSONObject().apply {
                    put("status", actualStatus)
                }

                NativeActionCoordinator.dispatchEvent(
                    activity,
                    eventClass,
                    payload.toString()
                )
            }
        }

        parentFragmentManager.beginTransaction().remove(this).commitAllowingStateLoss()
    }

    override fun onResume() {
        super.onResume()
        if (!hasLaunched && Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            hasLaunched = true
            launcher.launch(Manifest.permission.POST_NOTIFICATIONS)
        }
    }

    companion object {
        fun launch(activity: FragmentActivity, eventClass: String) {
            // Prevent duplicate fragments
            if (activity.supportFragmentManager.findFragmentByTag("push_notifications_permission_watcher_fragment") != null) {
                return
            }

            val fragment = PermissionFragment().apply {
                arguments = Bundle().apply {
                    putString("eventClass", eventClass)
                }
            }
            activity.supportFragmentManager
                .beginTransaction()
                .add(fragment, "push_notifications_permission_watcher_fragment")
                .commitAllowingStateLoss()
        }
    }
}

object PushNotificationsPermissionWatcherFunctions {

    class Watch(private val activity: FragmentActivity) : BridgeFunction {
        override fun execute(parameters: Map<String, Any>): Map<String, Any> {
            val eventClass = "GTCrais\\Native\\PushNotificationsPermissionWatcher\\Events\\PushNotificationsPermissionChanged"

            if (Build.VERSION.SDK_INT < Build.VERSION_CODES.TIRAMISU) {
                val enabled = NotificationManagerCompat.from(activity).areNotificationsEnabled()
                val status = if (enabled) "granted" else "denied"
                dispatchEvent(activity, status, eventClass)
                return BridgeResponse.success(mapOf("status" to status))
            }

            if (ContextCompat.checkSelfPermission(activity, Manifest.permission.POST_NOTIFICATIONS)
                == PackageManager.PERMISSION_GRANTED) {
                dispatchEvent(activity, "granted", eventClass)
                return BridgeResponse.success(mapOf("status" to "granted"))
            }

            // Mark permission as requested so resolvePermissionStatus can distinguish denied from not_determined
            activity.applicationContext
                .getSharedPreferences("push_notifications", Context.MODE_PRIVATE)
                .edit()
                .putBoolean("permission_requested", true)
                .apply()

            // Use a headless Fragment to request permission - ActivityResultLauncher
            // must be registered before the lifecycle owner reaches STARTED state
            activity.runOnUiThread {
                PermissionFragment.launch(activity, eventClass)
            }

            return BridgeResponse.success(mapOf("status" to "pending"))
        }
    }

    class CheckPermission(private val context: Context) : BridgeFunction {
        override fun execute(parameters: Map<String, Any>): Map<String, Any> {
            val status = resolvePermissionStatus(context)
            return BridgeResponse.success(mapOf("status" to status))
        }
    }

    fun resolvePermissionStatus(context: Context): String {
        return if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            val hasPermission = ContextCompat.checkSelfPermission(
                context,
                Manifest.permission.POST_NOTIFICATIONS
            ) == PackageManager.PERMISSION_GRANTED

            if (hasPermission) {
                "granted"
            } else {
                val prefs = context.getSharedPreferences("push_notifications", Context.MODE_PRIVATE)
                val hasRequested = prefs.getBoolean("permission_requested", false)
                if (hasRequested) "denied" else "not_determined"
            }
        } else {
            if (NotificationManagerCompat.from(context).areNotificationsEnabled()) "granted" else "denied"
        }
    }

    private fun dispatchEvent(activity: FragmentActivity, status: String, eventClass: String) {
        Handler(Looper.getMainLooper()).post {
            val payload = JSONObject().apply {
                put("status", status)
            }

            NativeActionCoordinator.dispatchEvent(
                activity,
                eventClass,
                payload.toString()
            )
        }
    }
}
